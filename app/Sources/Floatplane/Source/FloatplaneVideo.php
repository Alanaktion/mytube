<?php

namespace App\Sources\Floatplane\Source;

use App\Exceptions\ImportException;
use App\Models\Channel;
use App\Models\Video;
use App\Sources\Floatplane\FloatplaneClient;
use App\Sources\SourceVideo;
use App\Traits\DownloadsImages;

class FloatplaneVideo implements SourceVideo
{
    use DownloadsImages;

    public function canonicalizeId(string $id): string
    {
        return $id;
    }

    public function import(string $id): Video
    {
        if (! FloatplaneClient::isConfigured()) {
            throw new ImportException('Floatplane session access is not configured.');
        }

        $data = FloatplaneClient::getVideoData($id);

        $thumbnailUrl = $this->downloadImage((string) $data['thumbnail'], 'thumbs/floatplane');
        $posterUrl = $this->downloadImage((string) $data['poster'], 'thumbs/floatplane');

        $channel = Channel::import('floatplane', $data['channel_url']);
        return $channel->videos()->create([
            'uuid' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'source_type' => 'floatplane',
            'duration' => $data['duration'],
            'published_at' => $data['published_at'],
            'thumbnail_url' => $thumbnailUrl,
            'poster_url' => $posterUrl,
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https:\/\/)?(www\.)?floatplane\.com\/post\/([0-9a-z_\-]{10})/i', $url, $matches)) {
            return $matches[3];
        }
        return null;
    }

    public function matchId(string $id): bool
    {
        return (bool)preg_match('/^[0-9a-z_\-]{10}$/i', $id);
    }

    public function matchFilename(string $filename): ?string
    {
        // Floatplane videos don't include IDs in the filename
        return null;
    }

    public function getSourceUrl(Video $video): string
    {
        return 'https://www.floatplane.com/post/' . $video->uuid;
    }

    public function getEmbedHtml(Video $video): ?string
    {
        return null;
    }

    protected function getSourceUrlFromId(string $id): string
    {
        return 'https://www.floatplane.com/post/' . $id;
    }
}
