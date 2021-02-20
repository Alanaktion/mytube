<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFavoriteVideo extends Model
{
    function user()
    {
        return $this->belongsTo(User::class);
    }

    function video()
    {
        return $this->belongsTo(Video::class);
    }
}
