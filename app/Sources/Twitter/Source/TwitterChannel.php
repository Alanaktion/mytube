<?php

namespace App\Sources\Twitter\Source;

use App\Exceptions\ImportException;
use App\Models\Channel;
use App\Sources\SourceChannel;
use App\Sources\Twitter\TwitterClient;
use App\Sources\YtDlp\YtDlpClient;
use App\Traits\DownloadsImages;

class TwitterChannel implements SourceChannel
{
    use DownloadsImages;

    public function getField(): string
    {
        return 'custom_url';
    }

    public function import(string $id): Channel
    {
        if (TwitterClient::isConfigured()) {
            $twitter = new TwitterClient();
            $channelData = $twitter->getUser($id);

            $imageUrl = $this->downloadImage($channelData->profile_image_url_https, 'thumbs/twitter-user');

            return Channel::create([
                'uuid' => $channelData->id_str,
                'title' => $channelData->name,
                'description' => $channelData->description,
                'custom_url' => $channelData->screen_name,
                'type' => 'twitter',
                'image_url' => $imageUrl,
                'image_url_lg' => $imageUrl,
                'published_at' => $channelData->created_at,
            ]);
        }

        $ytdl = new YtDlpClient();
        if (!$ytdl->isAvailable()) {
            throw new ImportException('Twitter API is not configured and yt-dlp is not available.');
        }

        $channelData = $ytdl->getChannelMetadata($this->getSourceUrlById($id));
        $image = YtDlpClient::getThumbnailUrl($channelData);
        $imageUrl = $image ? $this->downloadImage($image, 'thumbs/twitter-user') : null;

        return Channel::create([
            'uuid' => (string) ($channelData['channel_id'] ?? $channelData['id'] ?? $id),
            'title' => (string) ($channelData['channel'] ?? $channelData['uploader'] ?? $channelData['title'] ?? $id),
            'description' => (string) ($channelData['description'] ?? ''),
            'custom_url' => (string) ($channelData['uploader_id'] ?? $channelData['display_id'] ?? $id),
            'type' => 'twitter',
            'image_url' => $imageUrl,
            'image_url_lg' => $imageUrl,
            'published_at' => YtDlpClient::getPublishedAt($channelData),
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https?:\/\/)?(www\.)?twitter\.com\/([^\/]+)/i', $url, $matches)) {
            return $matches[3];
        }
        return null;
    }

    public function getSourceUrl(Channel $channel): string
    {
        return 'https://twitter.com/' . $channel->custom_url;
    }

    protected function getSourceUrlById(string $id): string
    {
        return 'https://twitter.com/' . $id;
    }
}
