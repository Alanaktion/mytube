<?php

namespace App\Models;

use App\Sources\Source;
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

    /**
     * @var string[]
     */
    protected $guarded = [];
    /**
     * @var string[]
     */
    protected $dates = ['published_at'];

    /**
     * @api
     */
    public static function import(string $type, string $id): Channel
    {
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            if ($source->getSourceType() === $type) {
                $field = $source->channel()->getField();

                // Check for existing previous import
                $channel = Channel::where($field, $id)
                    ->where('type', $type)
                    ->first();
                if ($channel) {
                    return $channel;
                }

                return $source->channel()->import($id);
            }
        }
        throw new Exception('Unable to import source type ' . $type);
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
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
    public function importVideos(): void
    {
        if ($this->type != 'youtube') {
            throw new Exception('Importing videos is not supported for this channel type.');
        }

        $ytdl = new YouTubeDlClient();
        if ($ytdl->getVersion()) {
            $ids = $ytdl->getChannelVideoIds($this->uuid);
            foreach ($ids as $id) {
                Video::import('youtube', $id);
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
    public function importPlaylists(bool $importItems = true): void
    {
        if ($this->type != 'youtube') {
            throw new Exception('Importing playlists is not supported for this channel type.');
        }

        $ytdl = new YouTubeDlClient();
        if ($ytdl->getVersion()) {
            $ids = $ytdl->getChannelPlaylistIds($this->uuid);
            foreach ($ids as $id) {
                $playlist = Playlist::import('youtube', $id);
                if ($importItems) {
                    $playlist->importItems();
                }
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
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            if ($source->getSourceType() == $this->type) {
                return $source->channel()->getSourceUrl($this);
            }
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

    public function source(): Source
    {
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            if ($source->getSourceType() == $this->type) {
                return $source;
            }
        }
    }
}
