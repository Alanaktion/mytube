<?php

namespace App\Models;

use App\Sources\Source;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use YoutubeDl\YoutubeDl;

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
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            if ($source->getSourceType() === $type) {
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

                break;
            }
        }
        if (!isset($video)) {
            throw new Exception('Unable to import source type ' . $type);
        }
        if ($filePath !== null && $video->file_path === null) {
            $video->file_path = $filePath;
            $video->save();
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
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            if ($source->getSourceType() == $this->source_type) {
                return $source->video()->getSourceUrl($this);
            }
        }
        return null;
    }

    public function getEmbedHtmlAttribute(): ?string
    {
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            if ($source->getSourceType() == $this->source_type) {
                return $source->video()->getEmbedHtml($this);
            }
        }
        return null;
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
     * Get a web-accessible path/URL to the source file.
     *
     * This currently creates a symlink to the source file in the public disk.
     *
     * @todo support remote file storage, e.g. storing video files on B2/S3.
     */
    public function getFileLinkAttribute(): ?string
    {
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

        // Set the base yt-dl configuration
        $ytdl = new YoutubeDl([
            'continue' => true,
            'format' => 'bestvideo+bestaudio[ext=m4a]/bestvideo+bestaudio/best',
            'merge-output-format' => 'mp4',
        ]);

        // Set download path
        $dir = Str::finish(config('app.ytdl.directory'), DIRECTORY_SEPARATOR);
        if ($downloadDir === null) {
            $this->loadMissing('channel:id,name');
            $dir .= Str::slug($this->channel->name);
        } else {
            $dir .= $downloadDir;
        }
        $ytdl->setDownloadPath($dir);

        // Download the video file and save the resulting path
        $result = $ytdl->download($this->source_link);
        $file = $result->getFile();
        $this->file_path = $file->getRealPath();
        $this->save();
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'user_favorite_videos')
            ->withTimestamps();
    }

    public function source(): Source
    {
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            if ($source->getSourceType() == $this->source_type) {
                return $source;
            }
        }
    }
}
