<?php

namespace App\Sources\Twitter\Source;

use App\Models\Channel;
use App\Models\Video;
use App\Sources\SourceVideo;
use App\Sources\Twitter\TwitterClient;
use App\Traits\DownloadsImages;
use Exception;
use Illuminate\Support\Str;

class TwitterVideo implements SourceVideo
{
    use DownloadsImages;

    public function canonicalizeId(string $id): string
    {
        return $id;
    }

    public function import(string $id): Video
    {
        $twitter = new TwitterClient();
        $data = $twitter->getStatus($id);

        if (!empty($data->errors)) {
            throw new Exception($data->errors[0]->message);
        }

        // Download images
        if (!empty($data->entities) && !empty($data->entities->media)) {
            $media = $data->entities->media[0];
            $url = substr($media->media_url_https, 0, -4);

            $file = 'thumbs/twitter/' . basename($url) . '-small.jpg';
            $params = [
                'format' => 'jpg',
                'name' => 'small',
            ];
            $thumbnailUrl = $this->downloadImage($url . '?' . http_build_query($params), $file);

            $file = 'thumbs/twitter/' . basename($media->media_url_https);
            $posterUrl = $this->downloadImage($url . '?' . http_build_query($params), $file);
        }

        $durationMs = $data->extended_entities->media[0]->video_info->duration_millis ?? null;

        // Create video
        $channel = Channel::import('twitter', $data->user->screen_name);
        return $channel->videos()->create([
            'uuid' => $data->id_str,
            'title' => Str::limit($data->full_text, 80, '…'),
            'description' => $data->full_text,
            'source_type' => 'twitter',
            'duration' => $durationMs ? floor($durationMs / 1000) : null,
            'published_at' => $data->created_at,
            'thumbnail_url' => $thumbnailUrl ?? null,
            'poster_url' => $posterUrl ?? null,
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https?:\/\/)?(www\.)?twitter\.com\/([^\/]+)\/status\/(\d{18,19})/i', $url, $matches)) {
            return $matches[4];
        }
        return null;
    }

    public function matchId(string $id): bool
    {
        return (bool)preg_match('/^\d{18,19}$/i', $id);
    }

    public function matchFilename(string $filename): ?string
    {
        if (preg_match('/-(\d{18,19})\.(mp4|m4v|avi|mkv|webm)$/i', $filename, $matches)) {
            return $matches[1];
        }
        if (preg_match('/ \[(\d{18,19})\]\.(mp4|m4v|avi|mkv|webm)$/i', $filename, $matches)) {
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
