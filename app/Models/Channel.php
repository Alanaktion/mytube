<?php

namespace App\Models;

use App\Clients\YouTube;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $guarded = [];
    protected $dates = ['published_at'];

    public static function importYouTube(string $id): Channel
    {
        $channel = Channel::where('uuid', $id)
            ->where('type', 'youtube')
            ->first();
        if ($channel) {
            return $channel;
        }

        $channelData = YouTube::getChannelData($id);
        return Channel::create([
            'uuid' => $channelData['id'],
            'title' => $channelData['title'],
            'description' => $channelData['description'],
            'custom_url' => $channelData['custom_url'],
            'country' => $channelData['country'],
            'type' => 'youtube',
            'published_at' => $channelData['published_at'],
        ]);
    }

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
