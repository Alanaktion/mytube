<?php

namespace App\Models;

use App\Exceptions\InvalidSourceException;
use App\Sources\Source;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
 * @property-read Channel $channel
 * @property-read \Illuminate\Database\Eloquent\Collection|Playlist[] $playlists
 * @property-read \Illuminate\Database\Eloquent\Collection|VideoFile[] $files
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $favoritedBy
 */
class Video extends Model
{
    use HasFactory;
    use Searchable;

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

        if ($filePath !== null && !VideoFile::where('path', $filePath)->exists()) {
            $video->files()->create([
                'path' => $filePath,
                'mime_type' => mime_content_type($filePath),
            ]);
        }
        return $video;
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $this->loadMissing('channel');
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'channel_title' => $this->channel->title,
            'description' => $this->description,
            'source_type' => $this->source_type,
            'channel_id' => $this->channel_id,
            'published_at' => $this->published_at,
        ];
    }

    public function getSourceLinkAttribute(): ?string
    {
        try {
            $source = source($this->source_type);
            return $source->video()->getSourceUrl($this);
        } catch (InvalidSourceException) {
            return null;
        }
    }

    public function getEmbedHtmlAttribute(): ?string
    {
        try {
            $source = source($this->source_type);
            return $source->video()->getEmbedHtml($this);
        } catch (InvalidSourceException) {
            return null;
        }
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_items');
    }

    public function files()
    {
        return $this->hasMany(VideoFile::class);
    }

    /**
     * @deprecated Use files relation instead.
     */
    public function getFilePathAttribute(): ?string
    {
        $file = $this->files()->first(['id', 'path']);
        if ($file) {
            return $file->path;
        }
        return null;
    }

    /**
     * @deprecated Use files relation instead.
     */
    public function getFileLinkAttribute(): ?string
    {
        $file = $this->files()->first(['id', 'path']);
        if ($file) {
            return $file->url;
        }
        return null;
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
                throw new Exception($video->getError());
            }
            $filePath = $video->getFile()->getRealPath();
            $this->files()->create([
                'path' => $filePath,
                'mime_type' => mime_content_type($filePath),
            ]);
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
