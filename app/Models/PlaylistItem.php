<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $playlist_id
 * @property int $video_id
 * @property string $uuid
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Playlist $playlist
 * @property-read Video $video
 */
class PlaylistItem extends Model
{
    /**
     * @var string[]
     */
    protected $guarded = [];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
