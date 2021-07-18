<?php

namespace App\Sources\Twitter\Source;

use App\Models\Channel;
use App\Models\Video;
use App\Sources\SourceVideo;
use App\Sources\Twitter\TwitterClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TwitterVideo implements SourceVideo
{
    public function canonicalizeId(string $id): string
    {
        return $id;
    }

    public function import(string $id): Video
    {
        $twitter = new TwitterClient();
        $data = $twitter->getStatus($id);

        // Download images
        if ($data->entities && $data->entities->media) {
            $media = $data->entities->media[0];
            $url = substr($media->media_url_https, 0, -4);

            $disk = Storage::disk('public');
            $file = 'thumbs/twitter/' . basename($url) . '-small.jpg';
            $params = [
                'format' => 'jpg',
                'name' => 'small',
            ];
            $disk->put($file, file_get_contents($url . '?' . http_build_query($params)), 'public');
            $thumbnailUrl = Storage::url('public/' . $file);

            $file = 'thumbs/twitter/' . basename($media->media_url_https);
            $disk->put($file, file_get_contents($media->media_url_https), 'public');
            $posterUrl = Storage::url('public/' . $file);
        }

        // Create video
        $channel = Channel::import('twitter', $data->user->screen_name);
        return $channel->videos()->create([
            'uuid' => $data->id_str,
            'title' => Str::limit($data->full_text, 80, '…'),
            'description' => $data->full_text,
            'source_type' => 'twitter',
            'published_at' => $data->created_at,
            'thumbnail_url' => $thumbnailUrl,
            'poster_url' => $posterUrl,
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https?:\/\/)?(www\.)?twitter\.com\/([^/]+)\/status\/([0-9]{19})/i', $url, $matches)) {
            return $matches[4];
        }
        return null;
    }

    public function matchId(string $id): bool
    {
        return (bool)preg_match('/^[0-9]{19}$/i', $id);
    }

    public function matchFilename(string $filename): ?string
    {
        if (preg_match('/-([0-9]{19})\.(mp4|m4v|avi|mkv|webm)$/i', $filename, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function getSourceUrl(Video $video): string
    {
        return 'https://twitter.com/' . $video->channel->custom_url . '/status/' . $video->uuid;
    }

    public function getEmbedHtml(Video $video): ?string
    {
        return view('sources.embed-twitter', ['video' => $video])->render();
    }
}
