<?php

namespace App\Sources\YouTube\Source;

use App\Models\Channel;
use App\Sources\SourceChannel;
use App\Sources\YouTube\YouTubeClient;
use App\Traits\DownloadsImages;

class YouTubeChannel implements SourceChannel
{
    use DownloadsImages;

    public function getField(): string
    {
        return 'uuid';
    }

    public function import(string $id): Channel
    {
        $channelData = YouTubeClient::getChannelData($id);

        // Download images
        if ($md = $channelData['thumbnails']->getMedium()) {
            $imageUrl = $this->downloadImage($md->url, "thumbs/youtube-channel/{$id}.jpg");
        }
        if ($lg = $channelData['thumbnails']->getMedium()) {
            $imageUrlLg = $this->downloadImage($md->url, "thumbs/youtube-channel-lg/{$id}.jpg");
        }

        // Create channel
        return Channel::create([
            'uuid' => $channelData['id'],
            'title' => $channelData['title'],
            'description' => $channelData['description'],
            'custom_url' => $channelData['custom_url'],
            'country' => $channelData['country'],
            'type' => 'youtube',
            'image_url' => $imageUrl ?? null,
            'image_url_lg' => $imageUrlLg ?? null,
            'published_at' => $channelData['published_at'],
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/https?:\/\/(www\.)?(youtube\.com|youtu\.be)\/channel\/([0-9a-z]{8,})/i', $url, $matches)) {
            return $matches[3];
        }
        return null;
    }

    public function getSourceUrl(Channel $channel): string
    {
        return 'https://www.youtube.com/channel/' . $channel->uuid;
    }
}
