<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFavoritePlaylist extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }
}
