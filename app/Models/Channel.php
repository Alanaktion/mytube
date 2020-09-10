<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $guarded = [];
    protected $dates = ['published_at'];

    public function getSourceLinkAttribute(): ?string
    {
        if ($this->type == 'youtube') {
            return 'https://www.youtube.com/channel/' . $this->uuid;
        }
        return null;
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }
}
