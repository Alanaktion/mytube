<?php

namespace App\Sources\Twitch\Source;

use App\Models\Channel;
use App\Models\Video;
use App\Sources\SourceVideo;
use App\Sources\Twitch\TwitchClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TwitchVideo implements SourceVideo
{
    public function canonicalizeId(string $id): string
    {
        return Str::start($id, 'v');
    }

    public function import(string $id): Video
    {
        $twitch = new TwitchClient();
        $data = $twitch->getVideos((int)ltrim($id, 'v'))[0];

        // Download images
        if ($data['thumbnail_url']) {
            $disk = Storage::disk('public');
            $url = str_replace(['%{width}', '%{height}'], [640, 360], $data['thumbnail_url']);
            $file = "thumbs/twitch/{$data['id']}.png";
            $disk->put($file, file_get_contents($url), 'public');
            $thumbnailUrl = Storage::url('public/' . $file);

            $url = str_replace(['%{width}', '%{height}'], [1280, 720], $data['thumbnail_url']);
            $file = "thumbs/twitch/{$data['id']}-poster.png";
            $disk->put($file, file_get_contents($url), 'public');
            $posterUrl = Storage::url('public/' . $file);
        } else {
            $thumbnailUrl = null;
            $posterUrl = null;
        }

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
        if (preg_match('/^(https?:\/\/)?(www\.)?twitch\.tv\/videos\/(v?[0-9]+)/i', $url, $matches)) {
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

    public function getSourceUrl(Video $video): string
    {
        return 'https://www.twitch.tv/videos/' . $video->uuid;
    }

    public function getEmbedHtml(Video $video): ?string
    {
        return view('sources.embed-twitch', ['video' => $video])->render();
    }
}
