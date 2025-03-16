<?php

namespace App\Sources\Test\Source;

use App\Models\Channel;
use App\Models\Video;
use App\Sources\SourceVideo;
use App\Traits\DownloadsImages;

class TestVideo implements SourceVideo
{
    use DownloadsImages;

    public function import(string $id): Video
    {
        $channel = Channel::import('test', '1'); // TODO: use random channel UUID
        return Video::create([
            'uuid' => $id,
            'channel_id' => $channel->id,
            // TODO: Add more fields
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        $pattern = '/^(https:\/\/)?example\.com\/([0-9A-Za-z_\-]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[2];
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
        return null;
    }

    public function getSourceUrl(Video $video): string
    {
        return 'https://www.example.com/' . $video->uuid;
    }

    public function getEmbedHtml(Video $video): ?string
    {
        return null;
    }
}
