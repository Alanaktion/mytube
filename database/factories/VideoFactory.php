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
            'source_visibility' => fn () => [
                Video::VISIBILITY_PUBLIC,
                Video::VISIBILITY_PUBLIC,
                Video::VISIBILITY_UNLISTED,
                Video::VISIBILITY_PRIVATE,
            ][random_int(0, 3)],
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'thumbnail_url' => fn (array $attributes) => "https://picsum.photos/seed/{$attributes['uuid']}/640/480",
            'poster_url' => fn (array $attributes) => "https://picsum.photos/seed/{$attributes['uuid']}/1280/720",
            'published_at' => now(),
        ];
    }
}
