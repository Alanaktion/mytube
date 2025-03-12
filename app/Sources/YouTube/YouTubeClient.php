<?php

namespace App\Sources\YouTube;

use App\Exceptions\ImportException;
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
     * @return array{id: string, channel_id: string, title: string, description: string, visibility: string, duration: string, published_at: string, is_livestream: true}
     */
    public static function getVideoData(string $id): array
    {
        /** @var YouTube $youtube */
        $youtube = App::make(YouTube::class);
        $response = $youtube->videos->listVideos(['snippet', 'status', 'contentDetails', 'liveStreamingDetails'], [
            'id' => $id,
        ]);
        usleep(1e5);
        foreach ($response as $video) {
            /** @var Video $video */
            return [
                'id' => $video->getId(),
                'channel_id' => $video->getSnippet()->getChannelId(),
                'title' => $video->getSnippet()->getTitle(),
                'description' => $video->getSnippet()->getDescription(),
                'visibility' => $video->getStatus()->getPrivacyStatus(),
                'duration' => $video->getContentDetails()->getDuration(),
                'published_at' => $video->getSnippet()->getPublishedAt(),
                'is_livestream' => (bool)$video->getLiveStreamingDetails(),
            ];
        }
        throw new ImportException('Video not found');
    }

    /**
     * Get metadata for a channel from YouTube by channel ID
     *
     * @link https://developers.google.com/youtube/v3/docs/channels
     * @return array{id: string, title: string, description: string, custom_url: string, country: string, published_at: string, thumbnails: \Google\Service\YouTube\ThumbnailDetails}
     */
    public static function getChannelData(string $id): array
    {
        /** @var YouTube $youtube */
        $youtube = App::make(YouTube::class);
        $params = $id[0] == '@' ? ['forUsername' => substr($id, 1)] : ['id' => $id];
        $response = $youtube->channels->listChannels('snippet', $params);
        usleep(1e5);
        foreach ($response as $channel) {
            /** @var Channel $channel */
            return [
                'id' => $channel->getId(),
                'title' => $channel->getSnippet()->getTitle(),
                'description' => $channel->getSnippet()->getDescription(),
                'custom_url' => $channel->getSnippet()->getCustomUrl(),
                'country' => $channel->getSnippet()->getCountry(),
                'published_at' => $channel->getSnippet()->getPublishedAt(),
                'thumbnails' => $channel->getSnippet()->getThumbnails(),
            ];
        }
        throw new ImportException('Channel not found');
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
     * @return array{id: string, channel_id: string, title: string, description: string, published_at: string}
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
                'id' => $playlist->getId(),
                'channel_id' => $playlist->getSnippet()->getChannelId(),
                'title' => $playlist->getSnippet()->getTitle(),
                'description' => $playlist->getSnippet()->getDescription(),
                'published_at' => $playlist->getSnippet()->getPublishedAt(),
            ];
        }
        throw new ImportException('Playlist not found');
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
