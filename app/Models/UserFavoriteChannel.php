<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    /**
     * @return BelongsTo<User,UserFavoriteChannel>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Channel,UserFavoriteChannel>
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
