<?php

namespace App\Clients;

use Carbon\Carbon;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Http;

class Floatplane
{
    public const BASE_URL = 'https://www.floatplane.com';

    protected static function request(string $url)
    {
        $jar = CookieJar::fromArray(
            [
                'sails.sid' => config('services.floatplane.session_id'),
            ],
            'www.floatplane.com'
        );

        return Http::withOptions([
            'base_uri' => self::BASE_URL,
            'cookies' => $jar,
        ])->get($url)->json();
    }

    public static function getVideoData(string $id): array
    {
        $result = self::request("/api/v3/content/post?id=$id");
        $images = collect($result['thumbnail']['childImages'])->sortBy('width');
        if ($images->count()) {
            $thumb = $images->first()['path'];
        } else {
            $parts = pathinfo($result['thumbnail']['path']);
            $thumb = "{$parts['dirname']}/{$parts['basename']}_400x225.{$parts['extension']}";
        }
        return [
            'id' => $result['id'],
            'title' => $result['title'],
            'channel_id' => $result['creator']['id'],
            'channel_url' => $result['creator']['urlname'],
            'description' => $result['videoAttachments'][0]['description'],
            'published_at' => new Carbon($result['releaseDate']),
            'poster' => $result['thumbnail']['path'],
            'thumbnail' => $thumb,
        ];
    }

    public static function getChannelData(string $creatorUrl): array
    {
        $result = self::request("/api/v2/creator/named?creatorURL[0]=$creatorUrl");
        $channel = $result[0];
        $images = collect($channel['icon']['childImages'])->sortBy('width');
        return [
            'id' => $channel['id'],
            'title' => $channel['title'],
            'description' => $channel['description'],
            'about' => $channel['about'],
            'custom_url' => $channel['urlname'],
            'icon' => $images->first()['path'],
            'icon-lg' => $channel['icon']['path'],
        ];
    }

    public static function getChannelPosts(string $uuid, int $limit = 20): array
    {
        // TODO: support offset to paginated results
        // TODO: clean up output to match what we can store
        $result = self::request("/api/v3/content/creator/list?ids[0]=$uuid&limit=$limit");
        return $result['blogPosts'];
    }
}
