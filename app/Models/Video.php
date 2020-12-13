<?php

namespace App\Models;

use App\Clients\YouTube;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use YoutubeDl\YoutubeDl;

class Video extends Model
{
    protected $guarded = [];
    protected $dates = ['published_at'];

    public static function importYouTube(string $id, ?string $filePath = null): Video
    {
        $video = Video::where('uuid', $id)
            ->whereHas('channel', function ($query) {
                $query->where('type', 'youtube');
            })
            ->first();
        if ($video) {
            if ($video->file_path === null && $filePath !== null) {
                $video->file_path = $filePath;
                $video->save();
            }
            return $video;
        }

        $data = YouTube::getVideoData($id);
        $channel = Channel::importYouTube($data['channel_id']);
        return $channel->videos()->create([
            'uuid' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'published_at' => $data['published_at'],
            'file_path' => $filePath,
        ]);
    }

    public function getSourceLinkAttribute(): ?string
    {
        if ($this->channel->type == 'youtube') {
            return 'https://www.youtube.com/watch?v=' . $this->uuid;
        }
        return null;
    }

    public function getThumbnailAttribute(): ?string
    {
        if (!$this->channel->type == 'youtube') {
            return null;
        }
        $disk = Storage::disk('public');
        $filePath = "thumbs/youtube/{$this->uuid}.jpg";
        if (!$disk->exists($filePath)) {
            return url('/images/thumbs/' . $this->uuid);
        }
        return $disk->url($filePath);
    }

    public function getPosterAttribute(): ?string
    {
        if (!$this->channel->type == 'youtube') {
            return null;
        }
        $disk = Storage::disk('public');
        $filePath = "thumbs/youtube-maxres/{$this->uuid}.jpg";
        if (!$disk->exists($filePath)) {
            return url('/images/posters/' . $this->uuid);
        }
        return $disk->url($filePath);
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
     * Get a web-accessible symlink to the source file.
     */
    public function link(): string
    {
        $ext = pathinfo($this->file_path, PATHINFO_EXTENSION);
        $file = "{$this->uuid}.{$ext}";

        // Ensure video directory exists
        if (!is_dir(storage_path('app/public/videos'))) {
            mkdir(storage_path('app/public/videos'), 0755, true);
        }

        // Create symlink if it doesn't exist
        $linkPath = storage_path('app/public/videos') . DIRECTORY_SEPARATOR . $file;
        if (!is_link($linkPath) && is_file($this->file_path)) {
            symlink($this->file_path, $linkPath);
        }

        return "/storage/videos/$file";
    }

    /**
     * Download thumbnail and poster images for a video.
     */
    public function downloadThumbs(): bool
    {
        if ($this->channel->type != 'youtube') {
            return false;
        }

        $disk = Storage::disk('public');
        $exists = $disk->exists("thumbs/youtube/{$this->uuid}.jpg");
        if (!$exists) {
            try {
                $data = file_get_contents("https://img.youtube.com/vi/{$this->uuid}/hqdefault.jpg");
                $disk->put("thumbs/youtube/{$this->uuid}.jpg", $data, 'public');

                $data = file_get_contents("https://img.youtube.com/vi/{$this->uuid}/maxresdefault.jpg");
                $disk->put("thumbs/youtube-maxres/{$this->uuid}.jpg", $data, 'public');
            } catch (\Exception $e) {
                Log::warning("Error downloading thumbnail {$this->uuid}: {$e->getMessage()}");
                return false;
            }
        }

        return true;
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
        $dl = new YoutubeDl([
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
        $dl->setDownloadPath($dir);

        // Download the video file and save the resulting path
        $result = $dl->download($this->source_link);
        $file = $result->getFile();
        $this->file_path = $file->getRealPath();
        $this->save();
    }
}
