<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $guarded = [];
    protected $dates = ['published_at'];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
