<?php

namespace App\Sources\Twitch\Source;

use App\Models\Channel;
use App\Sources\SourceChannel;
use App\Sources\Twitch\TwitchClient;
use Illuminate\Support\Facades\Storage;

class TwitchChannel implements SourceChannel
{
    public function getField(): string
    {
        return 'custom_url';
    }

    /**
     * Import a Twitch channel by custom URL (username).
     */
    public function import(string $id): Channel
    {
        $twitch = new TwitchClient();
        $channelData = $twitch->getUser($id);

        // Download images
        $disk = Storage::disk('public');
        $data = file_get_contents($channelData['profile_image_url']);
        $file = 'thumbs/twitch-user/' . basename($channelData['profile_image_url']);
        $disk->put($file, $data, 'public');
        $imageUrl = Storage::url('public/' . $file);

        // Create channel
        return Channel::create([
            'uuid' => $channelData['id'], // may want a Twitch prefix or something, this is an int
            'title' => $channelData['display_name'],
            'description' => $channelData['description'],
            'custom_url' => $channelData['login'],
            'type' => 'twitch',
            'image_url' => $imageUrl,
            'image_url_lg' => $imageUrl,
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
}
