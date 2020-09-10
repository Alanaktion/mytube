<?php

namespace App\Clients;

use Exception;
use Illuminate\Support\Facades\App;

class YouTube
{
    /**
     * Get metadata for a video from YouTube by video ID
     *
     * @link https://developers.google.com/youtube/v3/docs/videos
     */
    public static function getVideoData(string $id): array
    {
        /** @var \Google_Service_YouTube $youtube */
        $youtube = App::make('Google_Service_YouTube');
        $response = $youtube->videos->listVideos('snippet', [
            'id' => $id,
        ]);
        usleep(1e5);
        foreach ($response as $video) {
            /** @var \Google_Service_YouTube_Video $video */
            return [
                'id' => $video->id,
                'channel_id' => $video->getSnippet()->channelId,
                'title' => $video->getSnippet()->title,
                'description' => $video->getSnippet()->description,
                'published_at' => $video->getSnippet()->publishedAt,
            ];
        }
        throw new Exception('Video not found');
    }

    /**
     * Get metadata for a channel from YouTube by channel ID
     *
     * @link https://developers.google.com/youtube/v3/docs/channels
     */
    public static function getChannelData(string $id): array
    {
        /** @var \Google_Service_YouTube $youtube */
        $youtube = App::make('Google_Service_YouTube');
        $response = $youtube->channels->listChannels('snippet', [
            'id' => $id,
        ]);
        usleep(1e5);
        foreach ($response as $channel) {
            /** @var \Google_Service_YouTube_Channel $channel */
            return [
                'id' => $channel->id,
                'title' => $channel->getSnippet()->title,
                'description' => $channel->getSnippet()->description,
                'custom_url' => $channel->getSnippet()->customUrl,
                'country' => $channel->getSnippet()->country,
                'published_at' => $channel->getSnippet()->publishedAt,
            ];
        }
        throw new Exception('Channel not found');
    }

    /**
     * Get metadata for a playlist by ID
     *
     * @link https://developers.google.com/youtube/v3/docs/playlists
     */
    public static function getPlaylistData(string $id): array
    {
        /** @var \Google_Service_YouTube $youtube */
        $youtube = App::make('Google_Service_YouTube');
        $response = $youtube->playlists->listPlaylists('snippet', [
            'id' => $id,
        ]);
        usleep(1e5);
        foreach ($response as $playlist) {
            /** @var \Google_Service_YouTube_Playlist $playlist */
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
     */
    public static function getPlaylistItemData(string $id): \Google_Service_YouTube_PlaylistItemListResponse
    {
        /** @var \Google_Service_YouTube $youtube */
        $youtube = App::make('Google_Service_YouTube');
        $response = $youtube->playlistItems->listPlaylistItems('snippet', [
            'playlistId' => $id,
        ]);
        usleep(1e5);
        return $response;
    }
}
