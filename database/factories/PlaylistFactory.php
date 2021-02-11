<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Playlist;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlaylistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Playlist::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => 'PL' . Str::random(32),
            'channel_id' => Channel::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'published_at' => now(),
        ];
    }
}
