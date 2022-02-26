<?php

namespace App\Models;

use App\Exceptions\InvalidSourceException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * @property int $id
 * @property int $channel_id
 * @property string $uuid
 * @property string $title
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $source_link
 * @property-read ?Channel $channel
 * @property-read \Illuminate\Database\Eloquent\Collection|PlaylistItem[] $items
 * @property-read PlaylistItem $firstItem
 */
class Playlist extends Model
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
    public static function import(string $type, string $id, bool $importItems = true): Playlist
    {
        $source = source($type);

        // Check for existing previous import
        $playlist = Playlist::where('uuid', $id)
            ->whereHas('channel', function ($query) use ($type): void {
                $query->where('type', $type);
            })
            ->first();
        if (!$playlist) {
            $playlist = $source->playlist()->import($id);
        }

        if ($importItems) {
            $source->playlist()->importItems($playlist);
        }
        return $playlist;
    }

    public function importItems(): void
    {
        $source = source($this->channel->type);
        $source->playlist()->importItems($this);
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $this->loadMissing('channel');
        $data = [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'channel_title' => $this->channel?->title,
            'description' => $this->description,
            'source_type' => $this->channel?->type,
            'channel_id' => $this->channel_id,
            'published_at' => $this->published_at,
        ];
        if ($this->channel === null) {
            unset($data['channel_title']);
            unset($data['source_type']);
        }
        return $data;
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param Builder<Channel> $query
     * @return Builder<Channel>
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('channel');
    }

    public function prepareIndex(): void
    {
        if (config('scout.driver') === 'meilisearch') {
            $index = $this->searchableUsing()->index($this->searchableAs());
            $index->updateFilterableAttributes(['channel_id', 'source_type']);
            $index->updateSortableAttributes(['published_at']);
        }
    }

    public function searchable()
    {
        parent::searchable();
        $this->prepareIndex();
    }

    public function sourceLink(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                try {
                    $source = $this->channel->source();
                    return $source->playlist()->getSourceUrl($this);
                } catch (InvalidSourceException) {
                    return null;
                }
            }
        );
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function items()
    {
        return $this->hasMany(PlaylistItem::class);
    }

    public function firstItem()
    {
        return $this->hasOne(PlaylistItem::class)->ofMany('position', 'min');
    }
}
