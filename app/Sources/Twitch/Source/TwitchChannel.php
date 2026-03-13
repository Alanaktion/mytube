<?php

namespace App\Sources\Twitch\Source;

use App\Exceptions\ImportException;
use App\Models\Channel;
use App\Sources\SourceChannel;
use App\Sources\YtDlp\YtDlpClient;
use App\Sources\Twitch\TwitchClient;
use App\Traits\DownloadsImages;

class TwitchChannel implements SourceChannel
{
    use DownloadsImages;

    public function getField(): string
    {
        return 'custom_url';
    }

    /**
     * Import a Twitch channel by custom URL (username).
     */
    public function import(string $id): Channel
    {
        if (TwitchClient::isConfigured()) {
            $twitch = new TwitchClient();
            $channelData = $twitch->getUser($id);

            $imageUrl = $this->downloadImage($channelData['profile_image_url'], 'thumbs/twitch-user');

            return Channel::create([
                'uuid' => $channelData['id'],
                'title' => $channelData['display_name'],
                'description' => $channelData['description'],
                'custom_url' => $channelData['login'],
                'type' => 'twitch',
                'image_url' => $imageUrl,
                'image_url_lg' => $imageUrl,
            ]);
        }

        $ytdl = new YtDlpClient();
        if (!$ytdl->isAvailable()) {
            throw new ImportException('Twitch API is not configured and yt-dlp is not available.');
        }

        $channelData = $ytdl->getChannelMetadata($this->getSourceUrlById($id));
        $image = YtDlpClient::getThumbnailUrl($channelData);
        $imageUrl = $image ? $this->downloadImage($image, 'thumbs/twitch-user') : null;

        return Channel::create([
            'uuid' => (string) ($channelData['channel_id'] ?? $channelData['id'] ?? $id),
            'title' => (string) ($channelData['channel'] ?? $channelData['uploader'] ?? $channelData['title'] ?? $id),
            'description' => (string) ($channelData['description'] ?? ''),
            'custom_url' => (string) ($channelData['uploader_id'] ?? $channelData['display_id'] ?? $id),
            'type' => 'twitch',
            'image_url' => $imageUrl,
            'image_url_lg' => $imageUrl,
            'published_at' => YtDlpClient::getPublishedAt($channelData),
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https?:\/\/)?(www\.)?twitch\.tv\/([\w-]+)$/i', $url, $matches)) {
            return $matches[3];
        }
        return null;
    }

    public function getSourceUrl(Channel $channel): string
    {
        return 'https://www.twitch.tv/' . $channel->custom_url;
    }

    protected function getSourceUrlById(string $id): string
    {
        return 'https://www.twitch.tv/' . $id;
    }
}
