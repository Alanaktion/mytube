<?php

namespace App\Sources\YouTube\Source;

use App\Models\Channel;
use App\Models\Video;
use App\Sources\SourceVideo;
use App\Sources\YouTube\YouTubeClient;
use App\Traits\DownloadsImages;

class YouTubeVideo implements SourceVideo
{
    use DownloadsImages;

    public function import(string $id): Video
    {
        $data = YouTubeClient::getVideoData($id);

        // Create video
        $channel = Channel::import('youtube', $data['channel_id']);
        return $channel->videos()->create([
            'uuid' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'source_type' => 'youtube',
            'source_visibility' => $data['visibility'],
            'is_livestream' => $data['is_livestream'],
            'published_at' => $data['published_at'],
            'thumbnail_url' => $this->downloadImage(
                "https://img.youtube.com/vi/{$id}/hqdefault.jpg",
                "thumbs/youtube/{$id}.jpg"
            ),
            'poster_url' => $this->downloadImage(
                "https://img.youtube.com/vi/{$id}/maxresdefault.jpg",
                "thumbs/youtube-maxres/{$id}.jpg"
            ),
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        $pattern = '/^(https:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([0-9A-Za-z_\-]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[4];
        }
        return null;
    }

    public function canonicalizeId(string $id): string
    {
        return $id;
    }

    public function matchId(string $id): bool
    {
        return (bool)preg_match('/^[0-9A-Za-z_\-]{11}$/', $id);
    }

    public function matchFilename(string $filename): ?string
    {
        if (preg_match('/-([A-Za-z0-9_\-]{11})\.(mp4|m4v|avi|mkv|webm)$/', $filename, $matches)) {
            return $matches[1];
        }
        if (preg_match('/ \[([A-Za-z0-9_\-]{11})\]/', $filename, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function getSourceUrl(Video $video): string
    {
        return 'https://www.youtube.com/watch?v=' . $video->uuid;
    }

    public function getEmbedHtml(Video $video): ?string
    {
        return view('sources.embed-youtube', ['video' => $video])->render();
    }
}
