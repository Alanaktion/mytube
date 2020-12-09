<?php

namespace App\Models;

use App\Clients\YouTube;
use App\Clients\YouTubeDl;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $guarded = [];
    protected $dates = ['published_at'];

    public static function importYouTube(
        string $id,
        bool $importVideos = false,
        bool $importPlaylists = false,
        ?bool $importPlaylistItems = null
    ): Channel {
        $channel = Channel::where('uuid', $id)
            ->where('type', 'youtube')
            ->first();
        if (!$channel) {
            $channelData = YouTube::getChannelData($id);
            $channel = Channel::create([
                'uuid' => $channelData['id'],
                'title' => $channelData['title'],
                'description' => $channelData['description'],
                'custom_url' => $channelData['custom_url'],
                'country' => $channelData['country'],
                'type' => 'youtube',
                'published_at' => $channelData['published_at'],
            ]);
        }

        if ($importVideos) {
            $channel->importVideos();
        }

        if ($importPlaylists) {
            $channel->importPlaylists($importPlaylistItems === null || $importPlaylistItems);
        }

        return $channel;
    }

    /**
     * Import any missing videos for this channel, up to 500.
     */
    public function importVideos()
    {
        $ytdl = new YouTubeDl();
        if ($ytdl->getVersion()) {
            $ids = $ytdl->getChannelVideoIds($this->uuid);
            foreach ($ids as $id) {
                Video::importYouTube($id);
            }
            return;
        }

        $videos = YouTube::getChannelVideos($this->uuid);
        foreach ($videos as $data) {
            /** @var Google_Service_YouTube_SearchResult $data */
            // TODO: fix to use search result correctly
            $this->videos()->firstOrCreate([
                'uuid' => $data['id'],
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'published_at' => $data['published_at'],
            ]);
        }
    }

    /**
     * Import any missing playlists for this channel, up to 500.
     */
    public function importPlaylists(bool $importItems = true)
    {
        $ytdl = new YouTubeDl();
        if ($ytdl->getVersion()) {
            $ids = $ytdl->getChannelPlaylistIds($this->uuid);
            foreach ($ids as $id) {
                $playlist = Playlist::importYouTube($id);
                if ($importItems) {
                    $playlist->importYouTubeItems();
                }
            }
            return;
        }

        $playlists = YouTube::getChannelPlaylists($this->uuid);
        foreach ($playlists as $data) {
            /** @var Google_Service_YouTube_SearchResult $data */
            // TODO: fix to use search result correctly
            $playlist = $this->playlists()->firstOrCreate([
                'uuid' => $data['id'],
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'published_at' => $data['published_at'],
            ]);

            if ($importItems) {
                $playlist->importYouTubeItems();
            }
        }
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
