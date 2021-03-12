<?php

namespace App\Sources\Twitch;

use App\Models\Channel;
use App\Models\Video;
use App\Sources\Source;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TwitchSource implements Source
{
    public function getSourceType(): string
    {
        return 'twitch';
    }

    public function getChannelField(): string
    {
        return 'custom_url';
    }

    public function canonicalizeVideo(string $id): string
    {
        return Str::start($id, 'v');
    }

    /**
     * Import a Twitch channel by custom URL (username).
     */
    public function importChannel(string $id): Channel
    {
        $channel = Channel::where('custom_url', $id)
            ->where('type', 'twitch')
            ->first();
        if (!$channel) {
            $twitch = new TwitchClient();
            $channelData = $twitch->getUser($id);

            // Download images
            $disk = Storage::disk('public');
            $data = file_get_contents($channelData['profile_image_url']);
            $file = 'thumbs/twitch-user/' . basename($channelData['profile_image_url']);
            $disk->put($file, $data, 'public');
            $imageUrl = Storage::url('public/' . $file);

            // Create channel
            $channel = Channel::create([
                'uuid' => $channelData['id'], // may want a Twitch prefix or something, this is an int
                'title' => $channelData['display_name'],
                'description' => $channelData['description'],
                'custom_url' => $channelData['login'],
                'type' => 'twitch',
                'image_url' => $imageUrl,
                'image_url_lg' => $imageUrl,
            ]);
        }

        return $channel;
    }

    public function importVideo(string $id): Video
    {
        $twitch = new TwitchClient();
        $data = $twitch->getVideos($id)[0];

        // Download images
        $disk = Storage::disk('public');
        $url = str_replace(['%{width}', '%{height}'], [640, 360], $data['thumbnail_url']);
        $file = "thumbs/twitch/{$data['id']}.png";
        $disk->put($file, file_get_contents($url), 'public');
        $thumbnailUrl = Storage::url('public/' . $file);

        $url = str_replace(['%{width}', '%{height}'], [1280, 720], $data['thumbnail_url']);
        $file = "thumbs/twitch/{$data['id']}-poster.png";
        $disk->put($file, file_get_contents($url), 'public');
        $posterUrl = Storage::url('public/' . $file);

        // Create video
        $channel = Channel::import('twitch', $data['user_login']);
        return $channel->videos()->create([
            'uuid' => Str::start($data['id'], 'v'),
            'title' => $data['title'],
            'description' => $data['description'],
            'source_type' => 'twitch',
            'published_at' => $data['published_at'],
            'thumbnail_url' => $thumbnailUrl,
            'poster_url' => $posterUrl,
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https:\/\/)?(www\.)?twitch\.tv\/videos\/(v?[0-9]+)/i', $url, $matches)) {
            return Str::start($matches[3], 'v');
        }
        return null;
    }

    public function matchId(string $id): bool
    {
        return (bool)preg_match('/^v?[0-9]{9}$/', $id);
    }

    public function matchFilename(string $filename): ?string
    {
        if (preg_match('/-v([0-9]{9})\.(mp4|m4v|avi|mkv|webm)$/', $filename, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
