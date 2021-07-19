<?php

namespace App\Sources\Twitter\Source;

use App\Models\Channel;
use App\Sources\SourceChannel;
use App\Sources\Twitter\TwitterClient;
use Illuminate\Support\Facades\Storage;

class TwitterChannel implements SourceChannel
{
    public function getField(): string
    {
        return 'custom_url';
    }

    public function import(string $id): Channel
    {
        $twitter = new TwitterClient();
        $channelData = $twitter->getUser($id);

        // Download images
        $disk = Storage::disk('public');
        $data = file_get_contents($channelData->profile_image_url_https);
        $file = 'thumbs/twitter-user/' . basename($channelData->profile_image_url_https);
        $disk->put($file, $data, 'public');
        $imageUrl = Storage::url('public/' . $file);

        // Create channel
        return Channel::create([
            'uuid' => $channelData->id_str, // may want a Twitter prefix or something, this is an int
            'title' => $channelData->name,
            'description' => $channelData->description,
            'custom_url' => $channelData->screen_name,
            'type' => 'twitter',
            'image_url' => $imageUrl,
            'image_url_lg' => $imageUrl,
            'published_at' => $channelData->created_at,
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
}
