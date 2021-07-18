<?php

namespace App\Models;

use Exception;
use App\Sources\YouTube\YouTubeClient;
use App\Sources\YouTube\YouTubeDlClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Channel extends Model
{
    use HasFactory;
    use Searchable;

    protected $guarded = [];
    protected $dates = ['published_at'];

    public static function import(string $type, string $id): Channel
    {
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            if ($source->getSourceType() == $type) {
                $field = $source->getChannelField();

                // Check for existing previous import
                $channel = Channel::where($field, $id)
                    ->where('type', $type)
                    ->first();
                if ($channel) {
                    return $channel;
                }

                return $source->importChannel($id);
            }
        }
        throw new Exception('Unable to import source type ' . $type);
    }

    /**
     * @deprecated
     */
    public static function importYouTube(
        string $id,
        bool $importVideos = false,
        bool $importPlaylists = false,
        ?bool $importPlaylistItems = null
    ): Channel {
        $channel = self::import('youtube', $id);

        if ($importVideos) {
            $channel->importVideos();
        }

        if ($importPlaylists) {
            $channel->importPlaylists($importPlaylistItems === null || $importPlaylistItems);
        }

        return $channel;
    }

    /**
     * @deprecated
     */
    public static function importFloatplane(string $url): Channel
    {
        return self::import('floatplane', $url);
    }

    /**
     * @deprecated
     */
    public static function importTwitch(string $url): Channel
    {
        return self::import('twitch', $url);
    }

    /**
     * @deprecated
     */
    public static function importTwitter(string $url): Channel
    {
        return self::import('twitter', $url);
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'source_type' => $this->type,
            'published_at' => $this->published_at,
        ];
    }

    /**
     * Import any missing videos for this channel
     *
     * If youtube-dl is not available, this will be limited to 500 items and
     * will consume a large amount of the API quota.
     */
    public function importVideos()
    {
        if ($this->type != 'youtube') {
            throw new Exception('Importing videos is not supported for this channel type.');
        }

        $ytdl = new YouTubeDlClient();
        if ($ytdl->getVersion()) {
            $ids = $ytdl->getChannelVideoIds($this->uuid);
            foreach ($ids as $id) {
                Video::importYouTube($id);
            }
            return;
        }

        $videos = YouTubeClient::getChannelVideos($this->uuid);
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
     * Import any missing playlists for this channel
     *
     * If youtube-dl is not available, this will be limited to 500 items and
     * will consume a large amount of the API quota.
     */
    public function importPlaylists(bool $importItems = true)
    {
        if ($this->type != 'youtube') {
            throw new Exception('Importing playlists is not supported for this channel type.');
        }

        $ytdl = new YouTubeDlClient();
        if ($ytdl->getVersion()) {
            $ids = $ytdl->getChannelPlaylistIds($this->uuid);
            foreach ($ids as $id) {
                $playlist = Playlist::importYouTube($id, $importItems);
            }
            return;
        }

        $playlists = YouTubeClient::getChannelPlaylists($this->uuid);
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
        if ($this->type == 'twitch') {
            return 'https://www.twitch.tv/' . $this->custom_url;
        }
        if ($this->type == 'floatplane') {
            return 'https://www.floatplane.com/channel/' . $this->custom_url;
        }
        if ($this->type == 'twitter') {
            return 'https://twitter.com/' . $this->custom_url;
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
