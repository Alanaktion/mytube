<?php

namespace App\Console\Commands;

use App\Models\Channel;
use App\Models\ImportError;
use App\Models\Playlist;
use App\Models\Video;
use Exception;
use Google_Service_YouTube;
use Illuminate\Console\Command;

class ImportYouTubePlaylist extends Command
{
    protected $signature = 'youtube:import-playlist {id*}';

    protected $description = 'Import YouTube videos from the filesystem.';

    protected $youtube;

    public function __construct(Google_Service_YouTube $youtube)
    {
        parent::__construct();
        $this->youtube = $youtube;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ids = $this->argument('id');

        foreach ($ids as $id) {
            if (!preg_match('/^PL[0-9a-z_-]{32}$/i', $id)) {
                $this->error('Invalid ID ' . $id);
                continue;
            }

            $this->info($id);
            $playlist = $this->importPlaylist($id);
            $this->importPlaylistItems($playlist);
        }

        return 0;
    }

    protected function importPlaylist(string $id): Playlist
    {
        if ($playlist = Playlist::where('uuid', $id)->first()) {
            return $playlist;
        }

        $data = $this->getPlaylistData($id);

        $channel = Channel::where('uuid', $data['channel_id'])->first();
        if (!$channel) {
            $channelData = $this->getChannelData($data['channel_id']);
            $channel = Channel::create([
                'uuid' => $channelData['id'],
                'title' => $channelData['title'],
                'description' => $channelData['description'],
                'custom_url' => $channelData['custom_url'],
                'country' => $channelData['country'],
                'type' => 'youtube',
                'published_at' => $channelData['published_at'],
            ]);
        }

        return $channel->playlists()->create([
            'uuid' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'published_at' => $data['published_at'],
        ]);
    }

    protected function getPlaylistData(string $id): array
    {
        $response = $this->youtube->playlists->listPlaylists('snippet', [
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

    protected function importPlaylistItems(Playlist $playlist): void
    {
        $playlist->load([
            'items:id,playlist_id,uuid',
        ]);

        $items = $this->getPlaylistItemData($playlist->uuid);
        $bar = $this->output->createProgressBar(count($items));
        foreach ($items as $item) {
            /** @var \Google_Service_YouTube_PlaylistItem $item */
            $bar->advance();
            if ($playlist->items->firstWhere('uuid', $item->id)) {
                // Skip existing playlist items
                continue;
            }

            $videoId = $item->getSnippet()->getResourceId()->videoId;
            $video = $this->importVideo($videoId, null);
            $playlist->items()->create([
                'video_id' => $video->id,
                'uuid' => $item->id,
                'position' => $item->getSnippet()->position
            ]);
        }
        $bar->finish();
    }

    protected function getPlaylistItemData(string $id): \Google_Service_YouTube_PlaylistItemListResponse
    {
        $response = $this->youtube->playlistItems->listPlaylistItems('snippet', [
            'playlistId' => $id,
        ]);
        usleep(1e5);
        return $response;
    }

    /**
     * Retrieve and store video metadata if it doesn't already exist
     */
    protected function importVideo(string $id, ?string $filePath = null): Video
    {
        if ($video = Video::where('uuid', $id)->first()) {
            return $video;
        }

        if (ImportError::where('uuid', $id)->first()) {
            throw new Exception('Video previously failed to import');
        }

        $data = $this->getVideoData($id);

        $channel = Channel::where('uuid', $data['channel_id'])->first();
        if (!$channel) {
            $channelData = $this->getChannelData($data['channel_id']);
            $channel = Channel::create([
                'uuid' => $channelData['id'],
                'title' => $channelData['title'],
                'description' => $channelData['description'],
                'custom_url' => $channelData['custom_url'],
                'country' => $channelData['country'],
                'type' => 'youtube',
                'published_at' => $channelData['published_at'],
            ]);
        }

        return $channel->videos()->create([
            'uuid' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'published_at' => $data['published_at'],
            'file_path' => $filePath,
        ]);
    }

    /**
     * Get metadata for a video from YouTube by video ID
     *
     * @link https://developers.google.com/youtube/v3/docs/videos
     */
    protected function getVideoData(string $id): array
    {
        $response = $this->youtube->videos->listVideos('snippet', [
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
    protected function getChannelData(string $id): array
    {
        $response = $this->youtube->channels->listChannels('snippet', [
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
}
