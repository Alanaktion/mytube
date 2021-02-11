<?php

namespace Database\Factories;

use App\Models\Playlist;
use App\Models\PlaylistItem;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlaylistItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlaylistItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'playlist_id' => Playlist::factory(),
            'video_id' => Video::factory(),
            'uuid' => Str::random(30),
            'position' => rand(0, 99),
        ];
    }
}
