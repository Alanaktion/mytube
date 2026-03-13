<?php

namespace App\Sources\Floatplane\Source;

use App\Exceptions\ImportException;
use App\Models\Channel;
use App\Sources\Floatplane\FloatplaneClient;
use App\Sources\SourceChannel;
use App\Traits\DownloadsImages;

class FloatplaneChannel implements SourceChannel
{
    use DownloadsImages;

    public function getField(): string
    {
        return 'custom_url';
    }

    public function import(string $id): Channel
    {
        if (FloatplaneClient::isConfigured()) {
            throw new ImportException('Floatplane session access is not configured.');
        }

        $channelData = FloatplaneClient::getChannelData($id);

        $imageUrl = $this->downloadImage($channelData['icon'], 'thumbs/floatplane-channel');
        $imageUrlLg = $this->downloadImage($channelData['icon-lg'], 'thumbs/floatplane-channel-lg');

        return Channel::create([
            'uuid' => $channelData['id'],
            'title' => $channelData['title'],
            'description' => $channelData['description'],
            'custom_url' => $channelData['custom_url'],
            'type' => 'floatplane',
            'image_url' => $imageUrl,
            'image_url_lg' => $imageUrlLg,
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https:\/\/)?(www\.)?floatplane\.com\/channel\/([0-9a-z_\-]{10})/i', $url, $matches)) {
            return $matches[3];
        }
        return null;
    }

    public function getSourceUrl(Channel $channel): string
    {
        return 'https://www.floatplane.com/channel/' . $channel->custom_url;
    }

    protected function getSourceUrlById(string $id): string
    {
        return 'https://www.floatplane.com/channel/' . $id;
    }
}
