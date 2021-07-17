<?php

namespace App\Models;

use App\Clients\Floatplane;
use App\Clients\Twitter;
use App\Clients\YouTube;
use App\Clients\YouTubeDl;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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

    public static function importYouTube(
        string $id,
        bool $importVideos = false,
        bool $importPlaylists = false,
        ?bool $importPlaylistItems = null
    ): Channel {
        /** @var Channel|null $channel */
        $channel = Channel::where('uuid', $id)
            ->where('type', 'youtube')
            ->first();
        if (!$channel) {
            $channelData = YouTube::getChannelData($id);
            /** @var Channel $channel */
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

    public static function importFloatplane(string $url): Channel
    {
        $channel = Channel::where('custom_url', $url)
            ->where('type', 'floatplane')
            ->first();
        if (!$channel) {
            $channelData = Floatplane::getChannelData($url);

            // Download images
            $disk = Storage::disk('public');
            $data = file_get_contents($channelData['icon']);
            $file = 'thumbs/floatplane-channel/' . basename($channelData['icon']);
            $disk->put($file, $data, 'public');
            $imageUrl = Storage::url('public/' . $file);
            $data = file_get_contents($channelData['icon-lg']);
            $file = 'thumbs/floatplane-channel/' . basename($channelData['icon-lg']);
            $disk->put($file, $data, 'public');
            $imageUrlLg = Storage::url('public/' . $file);

            // Create channel
            $channel = Channel::create([
                'uuid' => $channelData['id'],
                'title' => $channelData['title'],
                'description' => $channelData['description'],
                'custom_url' => $channelData['custom_url'],
                'type' => 'floatplane',
                'image_url' => $imageUrl,
                'image_url_lg' => $imageUrlLg,
            ]);
        }

        return $channel;
    }

    /**
     * @deprecated
     */
    public static function importTwitch(string $url): Channel
    {
        return self::import('twitch', $url);
    }

    public static function importTwitter(string $url): Channel
    {
        $channel = Channel::where('custom_url', $url)
            ->where('type', 'twitter')
            ->first();

        if (!$channel) {
            $twitter = new Twitter();
            $channelData = $twitter->getUser($url);

            // Download images
            $disk = Storage::disk('public');
            $data = file_get_contents($channelData->profile_image_url_https);
            $file = 'thumbs/twitter-user/' . basename($channelData->profile_image_url_https);
            $disk->put($file, $data, 'public');
            $imageUrl = Storage::url('public/' . $file);

            // Create channel
            $channel = Channel::create([
                'uuid' => $channelData->id_str, // may want a Twitter prefix or something, this is an int
                'title' => $channelData->name,
                'description' => $channelData->description,
                'custom_url' => $channelData->screen_name,
                'type' => 'twitter',
                'image_url' => $imageUrl,
                'image_url_lg' => $imageUrl,
                'published_at' => $channelData->created_at,
            ]);
        }

        return $channel;
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

        $ytdl = new YouTubeDl();
        if ($ytdl->getVersion()) {
            $ids = $ytdl->getChannelPlaylistIds($this->uuid);
            foreach ($ids as $id) {
                $playlist = Playlist::importYouTube($id, $importItems);
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
