<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFavoriteChannel extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
