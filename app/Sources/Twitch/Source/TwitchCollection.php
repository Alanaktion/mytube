<?php

namespace App\Sources\Twitch\Source;

use App\Exceptions\ImportException;
use App\Models\ImportError;
use App\Models\Playlist;
use App\Models\Video;
use App\Sources\YtDlp\YtDlpClient;
use Illuminate\Support\Facades\Log;
use App\Sources\SourcePlaylist;

class TwitchCollection implements SourcePlaylist
{
    public function import(string $id): Playlist
    {
        $ytdl = new YtDlpClient();
        if (!$ytdl->isAvailable()) {
            throw new ImportException('Twitch collections require yt-dlp because the API does not support them.');
        }

        $data = $ytdl->getPlaylistMetadata($this->getSourceUrlById($id));
        $channelImportId = (string) ($data['uploader_id'] ?? $data['channel_id'] ?? $data['display_id'] ?? '');
        $channel = $channelImportId !== '' ? \App\Models\Channel::import('twitch', $channelImportId) : null;

        return Playlist::create([
            'channel_id' => $channel?->id,
            'uuid' => (string) ($data['id'] ?? $id),
            'title' => (string) ($data['title'] ?? $id),
            'description' => (string) ($data['description'] ?? ''),
            'published_at' => YtDlpClient::getPublishedAt($data) ?? now(),
        ]);
    }

    public function importItems(Playlist $playlist): void
    {
        $ytdl = new YtDlpClient();
        if (!$ytdl->isAvailable()) {
            throw new ImportException('Twitch collections require yt-dlp because the API does not support them.');
        }

        $videoIds = $ytdl->getPlaylistVideoIds($this->getSourceUrl($playlist));
        $playlist->items()->delete();

        foreach ($videoIds as $position => $videoId) {
            try {
                $video = Video::import('twitch', $videoId);
                $playlist->items()->create([
                    'video_id' => $video->id,
                    'uuid' => 'ytdlp:' . sha1($playlist->uuid . ':' . $position . ':' . $videoId),
                    'position' => $position,
                ]);
            } catch (\Exception $e) {
                ImportError::updateOrCreate([
                    'uuid' => (string) $videoId,
                    'type' => 'twitch',
                ], [
                    'reason' => $e->getMessage(),
                ]);
                Log::warning('Failed importing Twitch collection item: ' . $videoId);
            }
        }
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https?:\/\/)?(www\.)?twitch\.tv\/collections\?([0-9a-z]{14,})$/i', $url, $matches)) {
            return $matches[3];
        }
        return null;
    }

    public function matchId(string $id): bool
    {
        // return (bool)preg_match('/^[0-9a-z]{14,}$/i', $id);
        return false;
    }

    public function getSourceUrl(Playlist $playlist): string
    {
        return 'https://www.twitch.tv/collections/' . $playlist->uuid;
    }

    protected function getSourceUrlById(string $id): string
    {
        return 'https://www.twitch.tv/collections/' . $id;
    }
}
