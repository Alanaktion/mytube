<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $guarded = [];
    protected $dates = ['published_at'];

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
