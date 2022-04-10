<?php

namespace App\Sources\YouTube\Source;

use App\Models\Channel;
use App\Models\ImportError;
use App\Models\Playlist;
use App\Models\Video;
use App\Sources\SourcePlaylist;
use App\Sources\YouTube\YouTubeClient;
use Illuminate\Support\Facades\Log;

class YouTubePlaylist implements SourcePlaylist
{
    public function import(string $id): Playlist
    {
        $data = YouTubeClient::getPlaylistData($id);
        $channel = Channel::import('youtube', $data['channel_id']);
        return $channel->playlists()->create([
            'uuid' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'published_at' => $data['published_at'],
        ]);
    }

    public function importItems(Playlist $playlist): void
    {
        $playlist->load('items:id,playlist_id,uuid');

        $items = YouTubeClient::getPlaylistItemData($playlist->uuid);
        foreach ($items as $item) {
            /** @var \Google_Service_YouTube_PlaylistItem $item */
            if ($dbItem = $playlist->items->firstWhere('uuid', $item->id)) {
                // Skip existing item, updating positions where necessary
                if ($dbItem->position != $item->getSnippet()->position) {
                    $dbItem->position = $item->getSnippet()->position;
                    $dbItem->save();
                }
                continue;
            }

            $videoId = $item->getSnippet()->getResourceId()->videoId;
            try {
                $video = Video::import('youtube', $videoId);
                $playlist->items()->create([
                    'video_id' => $video->id,
                    'uuid' => $item->id,
                    'position' => $item->getSnippet()->position,
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
        }
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https?:\/\/)?(www\.)?youtube\.com\/playlist\?list=(PL[0-9a-z_-]{16,32})$/i', $url, $matches)) {
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
}
