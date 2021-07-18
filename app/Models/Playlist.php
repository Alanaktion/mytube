<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Playlist extends Model
{
    use HasFactory;
    use Searchable;

    protected $guarded = [];
    protected $dates = ['published_at'];

    public static function import(string $type, string $id): Playlist
    {
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            if ($source->getSourceType() == $type) {
                // Check for existing previous import
                $playlist = Playlist::where('uuid', $id)
                    ->whereHas('channel', function ($query) use ($type) {
                        $query->where('type', $type);
                    })
                    ->first();
                if ($playlist) {
                    return $playlist;
                }

                return $source->importPlaylist($id);
            }
        }
        throw new Exception('Unable to import source type ' . $type);
    }

    public function importItems(): void
    {
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            if ($source->getSourceType() == $this->channel->type) {
                $source->importPlaylistItems($this);
            }
        }
    }

    /**
     * @deprecated
     */
    public static function importYouTube(string $id, bool $importItems = true): Playlist
    {
        $playlist = self::import('youtube', $id);

        if ($importItems) {
            $playlist->importYouTubeItems();
        }

        return $playlist;
    }

    public function toSearchableArray()
    {
        $this->loadMissing('channel');
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

    /**
     * @deprecated
     */
    public function importYouTubeItems()
    {
        $this->importItems();
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
