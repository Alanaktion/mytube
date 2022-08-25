<?php

namespace App\Sources\Twitch\Source;

use App\Models\Channel;
use App\Models\Video;
use App\Sources\SourceVideo;
use App\Sources\Twitch\TwitchClient;
use App\Traits\DownloadsImages;
use Illuminate\Support\Str;

class TwitchVideo implements SourceVideo
{
    use DownloadsImages;

    public function canonicalizeId(string $id): string
    {
        return Str::start($id, 'v');
    }

    public function import(string $id): Video
    {
        $twitch = new TwitchClient();
        $data = $twitch->getVideos((int)ltrim($id, 'v'))[0];

        // Download images
        if ($data['thumbnail_url']) {
            $url = str_replace(['%{width}', '%{height}'], [640, 360], $data['thumbnail_url']);
            $thumbnailUrl = $this->downloadImage($url, "thumbs/twitch/{$data['id']}.png");

            $url = str_replace(['%{width}', '%{height}'], [1280, 720], $data['thumbnail_url']);
            $posterUrl = $this->downloadImage($url, "thumbs/twitch/{$data['id']}-poster.png");
        } else {
            $thumbnailUrl = null;
            $posterUrl = null;
        }

        // Create video
        $channel = Channel::import('twitch', $data['user_login']);
        return $channel->videos()->create([
            'uuid' => Str::start($data['id'], 'v'),
            'title' => $data['title'],
            'description' => $data['description'],
            'source_type' => 'twitch',
            'duration' => $this->formatDuration($data['duration']),
            'is_livestream' => true,
            'published_at' => $data['published_at'],
            'thumbnail_url' => $thumbnailUrl,
            'poster_url' => $posterUrl,
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https?:\/\/)?(www\.)?twitch\.tv\/videos\/(v?\d{9,10}+)/i', $url, $matches)) {
            return Str::start($matches[3], 'v');
        }
        return null;
    }

    public function matchId(string $id): bool
    {
        return (bool)preg_match('/^v?\d{9,10}$/', $id);
    }

    public function matchFilename(string $filename): ?string
    {
        if (preg_match('/-v(\d{9,10})\.(mp4|m4v|avi|mkv|webm)$/', $filename, $matches)) {
            return $matches[1];
        }
        if (preg_match('/ \[v(\d{9,10})\]\.(mp4|m4v|avi|mkv|webm)$/', $filename, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function getSourceUrl(Video $video): string
    {
        return 'https://www.twitch.tv/videos/' . $video->uuid;
    }

    public function getEmbedHtml(Video $video): ?string
    {
        return view('sources.embed-twitch', ['video' => $video])->render();
    }

    /**
     * Convert Twitch duration format to seconds.
     */
    protected function formatDuration(?string $duration): ?int
    {
        if (!$duration) {
            return null;
        }
        preg_match('/^((\d+)d)?((\d+)h)?((\d+)m)?((\d+)s)?$/', $duration, $matches);
        $days = $matches[2] ?: 0;
        $hours = $matches[4] ?: 0;
        $minutes = $matches[6] ?: 0;
        $seconds = $matches[8] ?: 0;
        return $days * 86400 + $hours * 3600 + $minutes * 60 + $seconds;
    }
}
