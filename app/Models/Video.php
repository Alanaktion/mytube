<?php

namespace App\Models;

use App\Exceptions\InvalidSourceException;
use App\Sources\Source;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

/**
 * @property int $id
 * @property int|null $channel_id
 * @property string $uuid
 * @property string $title
 * @property string $description
 * @property string $source_type
 * @property string $source_visibility
 * @property bool $is_livestream
 * @property string|null $file_path
 * @property string|null $thumbnail_url
 * @property string|null $poster_url
 * @property \Illuminate\Support\Carbon $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $source_link
 * @property-read string $embed_html
 * @property-read string $file_link
 * @property-read ?Channel $channel
 * @property-read \Illuminate\Database\Eloquent\Collection|Playlist[] $playlists
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $favoritedBy
 */
class Video extends Model
{
    use HasFactory;
    use Searchable {
        searchable as scoutSearchable;
    }

    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_UNLISTED = 'unlisted';
    public const VISIBILITY_PRIVATE = 'private';

    /**
     * @var string[]
     */
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $dates = ['published_at'];

    /**
     * @api
     */
    public static function import(
        string $type,
        string $id,
        ?string $filePath = null
    ): Video {
        $source = source($type);
        $id = $source->video()->canonicalizeId($id);

        // Check for existing previous import
        $video = Video::where('uuid', $id)
            ->whereHas('channel', function ($query) use ($type): void {
                $query->where('type', $type);
            })
            ->first();

        // Import from the source if the video was not found
        if ($video === null) {
            $video = $source->video()->import($id);
        }

        if ($filePath !== null && $video->file_path === null) {
            $video->file_path = $filePath;
            $video->save();
        }

        // Remove any previous import errors
        ImportError::where('uuid', $id)
            ->where('type', $type)
            ->delete();

        return $video;
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $this->loadMissing('channel');
        $data = [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'channel_title' => $this->channel?->title,
            'description' => $this->description,
            'source_type' => $this->source_type,
            'channel_id' => $this->channel_id,
            'published_at' => $this->published_at,
        ];
        if ($this->channel === null) {
            unset($data['channel_title']);
        }
        return $data;
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param Builder<Video> $query
     * @return Builder<Video>
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('channel');
    }

    public function prepareIndex(): void
    {
        if (config('scout.driver') === 'meilisearch') {
            $index = $this->searchableUsing()->index($this->searchableAs());
            $index->updateFilterableAttributes(['channel_id', 'source_type']);
            $index->updateSortableAttributes(['published_at']);
        }
    }

    public function searchable()
    {
        $this->scoutSearchable();
        $this->prepareIndex();
    }

    /**
     * @return Attribute<?string,void>
     */
    public function sourceLink(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                try {
                    $source = $this->source();
                    return $source->video()->getSourceUrl($this);
                } catch (InvalidSourceException) {
                    return null;
                }
            },
        );
    }

    /**
     * @return Attribute<?string,void>
     */
    public function embedHtml(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                try {
                    $source = $this->source();
                    return $source->video()->getEmbedHtml($this);
                } catch (InvalidSourceException) {
                    return null;
                }
            },
        );
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_items');
    }


    /**
     * @return Attribute<?string,void>
     */
    public function fileLink(): Attribute
    {
        return new Attribute(
            /**
             * Get a web-accessible path/URL to the source file.
             *
             * This currently creates a symlink to the source file in the public disk.
             *
             * @todo support remote file storage, e.g. storing video files on B2/S3.
             */
            get: function (): ?string {
                if (!$this->file_path) {
                    return null;
                }

                $ext = pathinfo($this->file_path, PATHINFO_EXTENSION);
                $file = "{$this->uuid}.{$ext}";

                // Ensure video directory exists
                Storage::makeDirectory('public/videos');

                // Create symlink if it doesn't exist
                $linkPath = storage_path('app/public/videos') . DIRECTORY_SEPARATOR . $file;
                if (!is_link($linkPath) && is_file($this->file_path)) {
                    symlink($this->file_path, $linkPath);
                }

                return "/storage/videos/$file";
            }
        );
    }

    /**
     * Download the video file from the source link
     *
     * If the file already exists, the download is skipped.
     *
     * @link https://github.com/norkunas/youtube-dl-php
     * @link https://unix.stackexchange.com/a/454072
     * @link https://github.com/ytdl-org/youtube-dl/issues/4886#issuecomment-334068157
     */
    public function downloadVideo(?string $downloadDir = null): void
    {
        if (!$this->source_link) {
            throw new Exception('Video does not have a usable source link');
        }

        // Exit if a file already exists
        if ($this->file_path && file_exists($this->file_path)) {
            return;
        }

        // Set download path
        $dir = Str::finish(config('app.ytdl.directory'), DIRECTORY_SEPARATOR);
        if ($downloadDir === null) {
            $this->loadMissing('channel:id,title');
            $dir .= Str::slug($this->channel->title);
        } else {
            $dir .= $downloadDir;
        }

        // Download the video file and save the resulting path
        $yt = new YoutubeDl();
        $collection = $yt->download(
            Options::create()
                ->continue(true)
                ->format('bestvideo+bestaudio[ext=m4a]/bestvideo+bestaudio/best')
                ->mergeOutputFormat('mp4')
                ->downloadPath($dir)
                ->url($this->source_link)
        );
        foreach ($collection->getVideos() as $video) {
            if ($video->getError() !== null) {
                throw new Exception($video->getError());
            }
            $this->file_path = $video->getFile()->getRealPath();
            $this->save();
            return;
        }
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'user_favorite_videos')
            ->withTimestamps();
    }

    public function source(): Source
    {
        return source($this->source_type);
    }
}
