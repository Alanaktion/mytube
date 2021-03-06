<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VideoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Video::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => Str::random(11),
            'channel_id' => Channel::factory(),
            'source_type' => function (array $attributes) {
                return Channel::find($attributes['channel_id'])->type;
            },
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'thumbnail_url' => $this->faker->imageUrl(),
            'poster_url' => $this->faker->imageUrl(1280, 720),
            'published_at' => now(),
        ];
    }
}
