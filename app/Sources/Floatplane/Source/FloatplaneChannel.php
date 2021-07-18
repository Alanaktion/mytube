<?php

namespace App\Sources\Floatplane\Source;

use App\Models\Channel;
use App\Sources\Floatplane\FloatplaneClient;
use App\Sources\SourceChannel;
use Illuminate\Support\Facades\Storage;

class FloatplaneChannel implements SourceChannel
{
    public function getField(): string
    {
        return 'custom_url';
    }

    public function import(string $id): Channel
    {
        $channelData = FloatplaneClient::getChannelData($id);

        // Download images
        $disk = Storage::disk('public');
        $data = file_get_contents($channelData['icon']);
        $file = 'thumbs/floatplane-channel/' . basename($channelData['icon']);
        $disk->put($file, $data, 'public');
        $imageUrl = Storage::url('public/' . $file);
        $data = file_get_contents($channelData['icon-lg']);
        $file = 'thumbs/floatplane-channel/' . basename($channelData['icon-lg']);
        $disk->put($file, $data, 'public');
        $imageUrlLg = Storage::url('public/' . $file);

        // Create channel
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
}
