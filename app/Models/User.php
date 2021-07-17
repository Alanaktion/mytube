<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function favoriteVideos()
    {
        return $this->belongsToMany(Video::class, 'user_favorite_videos')
            ->withTimestamps();
    }

    public function favoritePlaylists()
    {
        return $this->belongsToMany(Playlist::class, 'user_favorite_playlists')
            ->withTimestamps();
    }

    public function favoriteChannels()
    {
        return $this->belongsToMany(Channel::class, 'user_favorite_channels')
            ->withTimestamps();
    }
}
