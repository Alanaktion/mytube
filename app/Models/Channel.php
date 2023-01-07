<?php

namespace App\Models;

use App\Exceptions\InvalidSourceException;
use App\Sources\Source;
use Exception;
use App\Sources\YouTube\YouTubeClient;
use App\Sources\YouTube\YouTubeDlClient;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property string $description
 * @property string|null $custom_url
 * @property string|null $country
 * @property string $type
 * @property string|null $image_url
 * @property string|null $image_url_lg
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $source_link
 * @property-read \Illuminate\Database\Eloquent\Collection|Video[] $videos
 * @property-read \Illuminate\Database\Eloquent\Collection|Playlist[] $playlists
 */
class Channel extends Model
{
    use HasFactory;
    use Searchable {
        searchable as scoutSearchable;
    }

    /**
     * @var string[]
     */
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $dates = ['published_at'];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * @api
     */
    public static function import(string $type, string $id): Channel
    {
        $source = source($type);
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
            'type' => $this->type,
            'published_at' => $this->published_at,
        ];
    }

    public function prepareIndex(): void
    {
        if (config('scout.driver') === 'meilisearch') {
            $index = $this->searchableUsing()->index($this->searchableAs());
            $index->updateFilterableAttributes(['type']);
            $index->updateSortableAttributes(['published_at']);
        }
    }

    public function searchable()
    {
        $this->scoutSearchable();
        $this->prepareIndex();
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
            /** @var \Google\Service\YouTube\SearchResult $data */
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
                Playlist::import('youtube', $id, $importItems);
            }
            return;
        }

        $playlists = YouTubeClient::getChannelPlaylists($this->uuid);
        foreach ($playlists as $data) {
            /** @var \Google\Service\YouTube\SearchResult $data */
            // TODO: fix to use search result correctly
            $playlist = $this->playlists()->firstOrCreate([
                'uuid' => $data['id'],
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'published_at' => $data['published_at'],
            ]);

            if ($importItems) {
                $playlist->importItems();
            }
        }
    }

    /**
     * @return Attribute<?string,void>
     */
    public function sourceLink(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                try {
                    $source = source($this->type);
                    return $source->channel()->getSourceUrl($this);
                } catch (InvalidSourceException) {
                    return null;
                }
            },
        );
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    public function jobDetails()
    {
        return $this->morphMany(JobDetail::class, 'model');
    }

    public function source(): Source
    {
        return source($this->type);
    }

    public function delete(bool $playlistVideos = false, bool $files = false)
    {
        if ($playlistVideos) {
            $this->playlists()->each(function (Playlist $playlist) use ($files) {
                $playlist->delete(true, $files);
            });
        }
        if ($files) {
            $this->videos()->each(function (Video $video) {
                $video->delete(true);
            });
        }
        $this->playlists()->delete();
        $this->videos()->delete();
        if ($this->image_url) {
            @unlink($this->image_url);
        }
        if ($this->image_url_lg) {
            @unlink($this->image_url_lg);
        }
        parent::delete();
    }
}
