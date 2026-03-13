<?php

namespace App\Sources\YouTube\Source;

use App\Exceptions\ImportException;
use App\Models\Channel;
use App\Models\ImportError;
use App\Models\Playlist;
use App\Models\Video;
use App\Sources\SourcePlaylist;
use App\Sources\YtDlp\YtDlpClient;
use App\Sources\YouTube\YouTubeClient;
use Illuminate\Support\Facades\Log;

class YouTubePlaylist implements SourcePlaylist
{
    public function import(string $id): Playlist
    {
        if (YouTubeClient::isConfigured()) {
            $data = YouTubeClient::getPlaylistData($id);
            $channel = Channel::import('youtube', $data['channel_id']);
            return $channel->playlists()->create([
                'uuid' => $data['id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'published_at' => $data['published_at'],
            ]);
        }

        $ytdl = new YtDlpClient();
        if (!$ytdl->isAvailable()) {
            throw new ImportException('YouTube API is not configured and yt-dlp is not available.');
        }

        $data = $ytdl->getPlaylistMetadata($this->getSourceUrlById($id));
        $channelImportId = $data['channel_id'] ?? null;
        if ((!is_string($channelImportId) || $channelImportId === '') && !empty($data['uploader_id'])) {
            $channelImportId = '@' . ltrim((string) $data['uploader_id'], '@');
        }
        if (!is_string($channelImportId) || $channelImportId === '') {
            throw new ImportException('yt-dlp did not return a YouTube playlist owner.');
        }

        $channel = Channel::import('youtube', $channelImportId);
        return $channel->playlists()->create([
            'uuid' => (string) ($data['id'] ?? $id),
            'title' => (string) ($data['title'] ?? $id),
            'description' => (string) ($data['description'] ?? ''),
            'published_at' => YtDlpClient::getPublishedAt($data) ?? now(),
        ]);
    }

    public function importItems(Playlist $playlist): void
    {
        $playlist->load('items:id,playlist_id,uuid');
        $detail = $playlist->jobDetails()->create([
            'type' => 'import_items',
        ]);

        if (YouTubeClient::isConfigured()) {
            $items = YouTubeClient::getPlaylistItemData($playlist->uuid);
            $detail->data = [
                'count' => count($items),
                'imported' => 0,
            ];
            $detail->save();

            foreach ($items as $index => $item) {
                /** @var \Google_Service_YouTube_PlaylistItem $item */
                $videoId = $item->getSnippet()->getResourceId()->videoId;
                try {
                    $video = Video::import('youtube', $videoId);
                    $playlist->items()->updateOrCreate([
                        'uuid' => $item->id,
                    ], [
                        'video_id' => $video->id,
                        'position' => (int) $item->getSnippet()->position,
                    ]);
                } catch (\Exception $e) {
                    ImportError::updateOrCreate([
                        'uuid' => $videoId,
                        'type' => 'youtube',
                    ], [
                        'reason' => $e->getMessage(),
                    ]);
                    Log::warning('Failed importing playlist item: ' . $videoId);
                }

                $detail->update([
                    'data->imported' => $index + 1,
                ]);
            }

            $detail->delete();
            return;
        }

        $ytdl = new YtDlpClient();
        if (!$ytdl->isAvailable()) {
            throw new ImportException('YouTube API is not configured and yt-dlp is not available.');
        }

        $videoIds = $ytdl->getPlaylistVideoIds($this->getSourceUrl($playlist));
        $detail->data = [
            'count' => count($videoIds),
            'imported' => 0,
        ];
        $detail->save();

        $playlist->items()->delete();
        foreach ($videoIds as $position => $videoId) {
            try {
                $video = Video::import('youtube', $videoId);
                $playlist->items()->create([
                    'video_id' => $video->id,
                    'uuid' => 'ytdlp:' . sha1($playlist->uuid . ':' . $position . ':' . $videoId),
                    'position' => $position,
                ]);
            } catch (\Exception $e) {
                ImportError::updateOrCreate([
                    'uuid' => $videoId,
                    'type' => 'youtube',
                ], [
                    'reason' => $e->getMessage(),
                ]);
                Log::warning('Failed importing playlist item: ' . $videoId);
            }

            $detail->update([
                'data->imported' => $position + 1,
            ]);
        }

        $detail->delete();
    }

    public function matchUrl(string $url): ?string
    {
        if (
            preg_match(
                '/^(https?:\/\/)?(www\.)?youtube\.com\/playlist\?list=(PL[0-9a-z_-]{16,32})$/i',
                $url,
                $matches,
            )
        ) {
            return $matches[3];
        }
        return null;
    }

    public function matchId(string $id): bool
    {
        return (bool)preg_match('/^(PL|LL|EC|UU|FL|RD|UL|TL|PU|OLAK5uy_)[0-9a-z_-]{10,}$/i', $id);
    }

    public function getSourceUrl(Playlist $playlist): string
    {
        return 'https://www.youtube.com/playlist?list=' . $playlist->uuid;
    }

    protected function getSourceUrlById(string $id): string
    {
        return 'https://www.youtube.com/playlist?list=' . $id;
    }
}
