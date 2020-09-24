<?php

namespace App\Models;

use App\Clients\YouTube;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Playlist extends Model
{
    protected $guarded = [];
    protected $dates = ['published_at'];

    public static function importYouTube(string $id, bool $importItems = true): Playlist
    {
        $playlist = Playlist::where('uuid', $id)
            ->whereHas('channel', function ($query) {
                $query->where('type', 'youtube');
            })
            ->first();
        if ($playlist) {
            return $playlist;
        }

        $data = YouTube::getPlaylistData($id);
        $channel = Channel::importYouTube($data['channel_id']);
        /** @var Playlist $playlist */
        $playlist = $channel->playlists()->create([
            'uuid' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'published_at' => $data['published_at'],
        ]);

        if ($importItems) {
            $playlist->importYouTubeItems();
        }

        return $playlist;
    }

    public function importYouTubeItems()
    {
        $this->load('items:id,playlist_id,uuid');

        $items = YouTube::getPlaylistItemData($this->uuid);
        foreach ($items as $item) {
            /** @var \Google_Service_YouTube_PlaylistItem $item */
            if ($this->items->firstWhere('uuid', $item->id)) {
                // Skip existing item
                continue;
            }

            $videoId = $item->getSnippet()->getResourceId()->videoId;
            try {
                $video = Video::importYouTube($videoId);
                $this->items()->create([
                    'video_id' => $video->id,
                    'uuid' => $item->id,
                    'position' => $item->getSnippet()->position
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed importing playlist item: ' . $videoId);
            }
        }
    }

    public function getSourceLinkAttribute(): ?string
    {
        if ($this->channel->type == 'youtube') {
            return 'https://www.youtube.com/playlist?list=' . $this->uuid;
        }
        return null;
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function items()
    {
        return $this->hasMany(PlaylistItem::class);
    }
}
