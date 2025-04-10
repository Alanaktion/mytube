<?php

namespace App\Sources\Floatplane;

use Carbon\Carbon;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Http;

class FloatplaneClient
{
    final public const BASE_URL = 'https://www.floatplane.com';

    /**
     * @return array|mixed
     */
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

    /**
     * @return array{id: mixed, title: mixed, channel_id: mixed, channel_url: mixed, description: mixed, duration: mixed, published_at: \Carbon\Carbon, poster: mixed, thumbnail: mixed}
     */
    public static function getVideoData(string $id): array
    {
        $result = self::request("/api/v3/content/post?id=$id");
        $childImages = $result['thumbnail']['childImages'];
        $images = collect($childImages)->sortBy('width');
        $thumb = $images->count() !== 0 ? $images->first()['path'] : $result['thumbnail']['path'];
        return [
            'id' => $result['id'],
            'title' => $result['title'],
            'channel_id' => $result['creator']['id'],
            'channel_url' => $result['creator']['urlname'],
            'description' => $result['videoAttachments'][0]['description'],
            'duration' => $result['metadata']['videoDuration'] ?? $result['metadata']['audioDuration'] ?? null,
            'published_at' => new Carbon($result['releaseDate']),
            'poster' => $result['thumbnail']['path'],
            'thumbnail' => $thumb,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function getChannelData(string $creatorUrl): array
    {
        $result = self::request("/api/v2/creator/named?creatorURL[0]=$creatorUrl");
        $channel = $result[0];
        $childImages = $channel['icon']['childImages'];
        $images = collect($childImages)->sortBy('width');
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

    /**
     * @return array<string, mixed>
     */
    public static function getChannelPosts(string $uuid, int $limit = 20): array
    {
        // TODO: support offset to paginated results
        // TODO: clean up output to match what we can store
        $result = self::request("/api/v3/content/creator/list?ids[0]=$uuid&limit=$limit");
        return $result['blogPosts'];
    }
}
