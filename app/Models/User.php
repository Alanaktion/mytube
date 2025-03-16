<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $role
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Video[] $favoriteVideos
 * @property-read \Illuminate\Database\Eloquent\Collection|Playlist[] $favoritePlaylists
 * @property-read \Illuminate\Database\Eloquent\Collection|Channel[] $favoriteChannels
 */
class User extends Authenticatable
{
    use HasApiTokens;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    final public const ROLE_USER = 'user';
    final public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * @return BelongsToMany<Video, $this>
     */
    public function favoriteVideos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'user_favorite_videos')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Playlist, $this>
     */
    public function favoritePlaylists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'user_favorite_playlists')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Channel, $this>
     */
    public function favoriteChannels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class, 'user_favorite_channels')
            ->withTimestamps();
    }
}
