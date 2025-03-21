<?php

namespace App\Models;

use App\Exceptions\ImportException;
use App\Exceptions\InvalidSourceException;
use App\Sources\Source;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
 * @property-read string|null $file_link
 * @property-read ?Channel $channel
 * @property-read \Illuminate\Database\Eloquent\Collection|Playlist[] $playlists
 * @property-read \Illuminate\Database\Eloquent\Collection|VideoFile[] $files
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $favoritedBy
 */
class Video extends Model
{
    /** @use HasFactory<\Database\Factories\VideoFactory> */
    use HasFactory;
    use Searchable {
        searchable as scoutSearchable;
    }

    final public const VISIBILITY_PUBLIC = 'public';
    final public const VISIBILITY_UNLISTED = 'unlisted';
    final public const VISIBILITY_PRIVATE = 'private';

    /**
     * @var string[]
     */
    protected $guarded = [];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

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

        if ($filePath !== null) {
            $video->addFile($filePath);
        }

        // Remove any previous import errors
        ImportError::where('uuid', $id)
            ->where('type', $type)
            ->delete();

        return $video;
    }

    /**
     * Add a file to the video, importing metadata when available.
     *
     * Previously added files will return the existing model.
     */
    public function addFile(string $filePath): VideoFile
    {
        $file = $this->files()->where('path', $filePath)->first();
        if ($file) {
            return $file;
        }
        $data = [
            'path' => $filePath,
            'mime_type' => mime_content_type($filePath),
            'size' => filesize($filePath),
        ];
        $meta = VideoFile::getMetadataForFile($filePath) ?? [];
        return $this->files()->create(array_merge($data, $meta));
    }

    /**
     * @return array{id: int, uuid: string, title: string, channel_title?: string|null, description: string, source_type: string, channel_id: int|null, published_at: \Illuminate\Support\Carbon}
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
        if (!$this->channel instanceof \App\Models\Channel) {
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

    public function searchable(): void
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

    /**
     * @return BelongsTo<Channel, $this>
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * @return BelongsToMany<Playlist, $this>
     */
    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'playlist_items');
    }

    /**
     * @return HasMany<VideoFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(VideoFile::class);
    }

    /**
     * @return MorphMany<JobDetail, $this>
     */
    public function jobDetails(): MorphMany
    {
        return $this->morphMany(JobDetail::class, 'model');
    }

    /**
     * @return Attribute<?string,void>
     * @deprecated Use files relation instead.
     */
    public function filePath(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                $file = $this->files()->first(['id', 'path']);
                if ($file) {
                    return $file->path;
                }
                return null;
            }
        );
    }

    /**
     * @return Attribute<?string,void>
     * @deprecated Use files relation instead.
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
                $file = $this->files()->first(['id', 'path']);
                if ($file) {
                    return $file->url;
                }
                return null;
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
        if ($this->source_link === '' || $this->source_link === '0') {
            throw new ImportException('Video does not have a usable source link');
        }

        // Exit if a file already exists
        if ($this->files()->count()) {
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
                throw new ImportException($video->getError());
            }
            $filePath = $video->getFile()->getRealPath();
            $this->files()->create([
                'path' => $filePath,
                'mime_type' => mime_content_type($filePath),
            ]);
            return;
        }
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorite_videos')
            ->withTimestamps();
    }

    public function source(): Source
    {
        return source($this->source_type);
    }

    public function delete(bool $files = false): ?bool
    {
        if ($files) {
            $this->files()->get()->each(function (VideoFile $file): void {
                $file->delete();
            });
        }
        if ($this->thumbnail_url) {
            @unlink($this->thumbnail_url);
        }
        if ($this->poster_url) {
            @unlink($this->poster_url);
        }
        return parent::delete();
    }
}
