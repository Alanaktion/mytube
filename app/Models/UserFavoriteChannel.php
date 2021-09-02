<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $channel_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @property-read Channel $channel
 */
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
