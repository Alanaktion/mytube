<?php

namespace App\Sources\YouTube;

use Exception;
use Google\Service\YouTube;
use Google\Service\YouTube\Channel;
use Google\Service\YouTube\Playlist;
use Google\Service\YouTube\PlaylistItem;
use Google\Service\YouTube\Video;
use Illuminate\Support\Facades\App;

class YouTubeClient
{
    /**
     * Get metadata for a video from YouTube by video ID
     *
     * @link https://developers.google.com/youtube/v3/docs/videos
     * @return array<string, mixed>
     */
    public static function getVideoData(string $id): array
    {
        /** @var YouTube $youtube */
        $youtube = App::make(YouTube::class);
        $response = $youtube->videos->listVideos(['snippet', 'status', 'liveStreamingDetails'], [
            'id' => $id,
        ]);
        usleep(1e5);
        foreach ($response as $video) {
            /** @var Video $video */
            return [
                'id' => $video->id,
                'channel_id' => $video->getSnippet()->channelId,
                'title' => $video->getSnippet()->title,
                'description' => $video->getSnippet()->description,
                'visibility' => $video->getStatus()->privacyStatus,
                'published_at' => $video->getSnippet()->publishedAt,
                'is_livestream' => (bool)$video->getLiveStreamingDetails(),
            ];
        }
        throw new Exception('Video not found');
    }

    /**
     * Get metadata for a channel from YouTube by channel ID
     *
     * @link https://developers.google.com/youtube/v3/docs/channels
     * @return array<string, mixed>
     */
    public static function getChannelData(string $id): array
    {
        /** @var YouTube $youtube */
        $youtube = App::make(YouTube::class);
        $response = $youtube->channels->listChannels('snippet', [
            'id' => $id,
        ]);
        usleep(1e5);
        foreach ($response as $channel) {
            /** @var Channel $channel */
            return [
                'id' => $channel->id,
                'title' => $channel->getSnippet()->title,
                'description' => $channel->getSnippet()->description,
                'custom_url' => $channel->getSnippet()->customUrl,
                'country' => $channel->getSnippet()->country,
                'published_at' => $channel->getSnippet()->publishedAt,
                'thumbnails' => $channel->getSnippet()->getThumbnails(),
            ];
        }
        throw new Exception('Channel not found');
    }

    /**
     * Get an array of videos on a channel
     *
     * @link https://developers.google.com/youtube/v3/docs/search/list
     *
     * @return Video[]
     */
    public static function getChannelVideos(string $id): array
    {
        return self::searchChannel($id, 'video');
    }

    /**
     * Get an array of playlists on a channel
     *
     * @link https://developers.google.com/youtube/v3/docs/search/list
     *
     * @return Playlist[]
     */
    public static function getChannelPlaylists(string $id): array
    {
        return self::searchChannel($id, 'playlist');
    }

    /**
     * Get search results for a type of item by channel ID
     *
     * This is hard-limited to 500 results on YouTube's end. It also has a quota
     * cost of 100 units, which is very high compared to e.g. listing playlist
     * items which only consumes 1 unit, so it should be avoided.
     *
     * Free accounts can call this endpoint ~20 times a day at best, so
     * paginating a full 500 results will consume 1000 units, or about half a
     * day's quota. A complete import of a single channel's playlist and videos
     * (up to what is allowed) can potentially consume an *entire* day's
     * API quota.
     *
     * @todo Investigate options for working around the 500 item limit, or
     *       using alternative methods like youtube-dl's page scraping.
     *
     * @link https://developers.google.com/youtube/v3/docs/search/list
     *
     * @return Video[]|Playlist[]
     */
    protected static function searchChannel(string $id, string $type): array
    {
        /** @var YouTube $youtube */
        $youtube = App::make(YouTube::class);
        $params = [
            'channelId' => $id,
            'order' => 'date',
            'type' => $type,
            'maxResults' => 50,
        ];
        $results = [];
        do {
            $response = $youtube->search->listSearch('snippet', $params);
            // TODO: normalize result structure to ease usage
            foreach ($response as $item) {
                $results[] = $item;
            }
            $params['pageToken'] = $response->nextPageToken;
            usleep(1e5);
            if (count($results) >= $response->getPageInfo()->totalResults) {
                break;
            }
        } while (true);

        return $results;
    }

    /**
     * Get metadata for a playlist by ID
     *
     * @link https://developers.google.com/youtube/v3/docs/playlists
     * @return array<string, string>
     */
    public static function getPlaylistData(string $id): array
    {
        /** @var YouTube $youtube */
        $youtube = App::make(YouTube::class);
        $response = $youtube->playlists->listPlaylists('snippet', [
            'id' => $id,
        ]);
        usleep(1e5);
        foreach ($response as $playlist) {
            /** @var Playlist $playlist */
            return [
                'id' => $playlist->id,
                'channel_id' => $playlist->getSnippet()->channelId,
                'title' => $playlist->getSnippet()->title,
                'description' => $playlist->getSnippet()->description,
                'published_at' => $playlist->getSnippet()->publishedAt,
            ];
        }
        throw new Exception('Playlist not found');
    }

    /**
     * Get playlist items by playlist ID
     *
     * @link https://developers.google.com/youtube/v3/docs/playlistItems
     *
     * @return PlaylistItem[]
     */
    public static function getPlaylistItemData(string $id): array
    {
        /** @var YouTube $youtube */
        $youtube = App::make(YouTube::class);
        $params = [
            'playlistId' => $id,
            'maxResults' => 50,
        ];
        $results = [];
        do {
            $response = $youtube->playlistItems->listPlaylistItems('snippet', $params);
            foreach ($response as $item) {
                $results[] = $item;
            }
            $params['pageToken'] = $response->nextPageToken;
            usleep(1e5);
            if (count($results) >= $response->getPageInfo()->totalResults) {
                break;
            }
        } while (true);

        return $results;
    }
}
