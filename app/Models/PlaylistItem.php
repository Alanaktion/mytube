<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaylistItem extends Model
{
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
