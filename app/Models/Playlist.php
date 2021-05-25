<?php

namespace App\Models;

use App\Clients\YouTube;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Searchable;

class Playlist extends Model
{
    use HasFactory;
    use Searchable;

    protected $guarded = [];
    protected $dates = ['published_at'];

    public static function importYouTube(string $id, bool $importItems = true): Playlist
    {
        $playlist = Playlist::where('uuid', $id)
            ->whereHas('channel', function ($query) {
                $query->where('type', 'youtube');
            })
            ->first();

        if (!$playlist) {
            $data = YouTube::getPlaylistData($id);
            $channel = Channel::importYouTube($data['channel_id']);
            /** @var Playlist $playlist */
            $playlist = $channel->playlists()->create([
                'uuid' => $data['id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'published_at' => $data['published_at'],
            ]);
        }

        if ($importItems) {
            $playlist->importYouTubeItems();
        }

        return $playlist;
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'channel_title' => $this->channel->title,
            'description' => $this->description,
            'source_type' => $this->channel->source_type,
            'channel_id' => $this->channel_id,
            'published_at' => $this->published_at,
        ];
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

    public function firstItem()
    {
        return $this->hasOne(PlaylistItem::class)->ofMany('position', 'min');
    }
}
