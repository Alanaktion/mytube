<?php

namespace App\Models;

use App\Clients\Floatplane;
use App\Clients\Twitch;
use App\Clients\Twitter;
use App\Clients\YouTube;
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
            'source_type' => 'youtube',
            'source_visibility' => $data['visibility'],
            'is_livestream' => $data['is_livestream'],
            'published_at' => $data['published_at'],
            'file_path' => $filePath,
        ]);
    }

    public static function importFloatplane(string $id, ?string $filePath = null): Video
    {
        $video = Video::where('uuid', $id)
            ->whereHas('channel', function ($query) {
                $query->where('type', 'floatplane');
            })
            ->first();
        if ($video) {
            if ($video->file_path === null && $filePath !== null) {
                $video->file_path = $filePath;
                $video->save();
            }
            return $video;
        }

        $data = Floatplane::getVideoData($id);

        // Download images
        $disk = Storage::disk('public');
        $file = 'thumbs/floatplane/' . basename($data['thumbnail']);
        $disk->put($file, file_get_contents($data['thumbnail']), 'public');
        $thumbnailUrl = Storage::url('public/' . $file);
        $file = 'thumbs/floatplane/' . basename($data['poster']);
        $disk->put($file, file_get_contents($data['poster']), 'public');
        $posterUrl = Storage::url('public/' . $file);

        // Create video
        $channel = Channel::importFloatplane($data['channel_url']);
        return $channel->videos()->create([
            'uuid' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'source_type' => 'floatplane',
            'published_at' => $data['published_at'],
            'file_path' => $filePath,
            'thumbnail_url' => $thumbnailUrl,
            'poster_url' => $posterUrl,
        ]);
    }

    public static function importTwitch(string $id, ?string $filePath = null): Video
    {
        $video = Video::where('uuid', Str::start($id, 'v'))
            ->whereHas('channel', function ($query) {
                $query->where('type', 'twitch');
            })
            ->first();
        if ($video) {
            if ($video->file_path === null && $filePath !== null) {
                $video->file_path = $filePath;
                $video->save();
            }
            return $video;
        }

        $twitch = new Twitch();
        $data = $twitch->getVideos($id)[0];

        // Download images
        if ($data['thumbnail_url']) {
            $disk = Storage::disk('public');
            $url = str_replace(['%{width}', '%{height}'], [640, 360], $data['thumbnail_url']);
            $file = "thumbs/twitch/{$data['id']}.png";
            $disk->put($file, file_get_contents($url), 'public');
            $thumbnailUrl = Storage::url('public/' . $file);

            $url = str_replace(['%{width}', '%{height}'], [1280, 720], $data['thumbnail_url']);
            $file = "thumbs/twitch/{$data['id']}-poster.png";
            $disk->put($file, file_get_contents($url), 'public');
            $posterUrl = Storage::url('public/' . $file);
        } else {
            $thumbnailUrl = null;
            $posterUrl = null;
        }

        // Create video
        $channel = Channel::importTwitch($data['user_login']);
        return $channel->videos()->create([
            'uuid' => Str::start($data['id'], 'v'),
            'title' => $data['title'],
            'description' => $data['description'],
            'source_type' => 'twitch',
            'published_at' => $data['published_at'],
            'file_path' => $filePath,
            'thumbnail_url' => $thumbnailUrl,
            'poster_url' => $posterUrl,
        ]);
    }

    public static function importTwitter(string $id, ?string $filePath = null): Video
    {
        $video = Video::where('uuid', Str::start($id, 'v'))
            ->whereHas('channel', function ($query) {
                $query->where('type', 'twitch');
            })
            ->first();
        if ($video) {
            if ($video->file_path === null && $filePath !== null) {
                $video->file_path = $filePath;
                $video->save();
            }
            return $video;
        }

        $twitter = new Twitter();
        $data = $twitter->getStatus($id);

        // Download images
        if ($data->entities && $data->entities->media) {
            $media = $data->entities->media[0];
            $url = substr($media->media_url_https, 0, -4);

            $disk = Storage::disk('public');
            $file = 'thumbs/twitter/' . basename($url) . '-small.jpg';
            $params = [
                'format' => 'jpg',
                'name' => 'small',
            ];
            $disk->put($file, file_get_contents($url . '?' . http_build_query($params)), 'public');
            $thumbnailUrl = Storage::url('public/' . $file);

            $file = 'thumbs/twitter/' . basename($media->media_url_https);
            $disk->put($file, file_get_contents($media->media_url_https), 'public');
            $posterUrl = Storage::url('public/' . $file);
        }

        // Create video
        $channel = Channel::importTwitter($data->user->screen_name);
        return $channel->videos()->create([
            'uuid' => $data->id_str,
            'title' => Str::limit($data->full_text, 80, '…'),
            'description' => $data->full_text,
            'source_type' => 'twitter',
            'published_at' => $data->created_at,
            'file_path' => $filePath,
            'thumbnail_url' => $thumbnailUrl,
            'poster_url' => $posterUrl,
        ]);
    }

    public function toSearchableArray()
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
        if ($this->source_type == 'youtube') {
            return 'https://www.youtube.com/watch?v=' . $this->uuid;
        }
        if ($this->source_type == 'twitch') {
            return 'https://www.twitch.tv/videos/' . $this->uuid;
        }
        if ($this->source_type == 'floatplane') {
            return 'https://www.floatplane.com/post/' . $this->uuid;
        }
        if ($this->source_type == 'twitter') {
            return 'https://twitter.com/' . $this->channel->custom_url . '/status/' . $this->uuid;
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
     * Get a web-accessible symlink to the source file.
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

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'user_favorite_videos')
            ->withTimestamps();
    }
}
