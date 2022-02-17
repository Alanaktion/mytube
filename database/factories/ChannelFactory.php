<?php

namespace Database\Factories;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ChannelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Channel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => Str::random(11),
            'title' => $this->faker->name,
            'description' => $this->faker->paragraph,
            'custom_url' => $this->faker->userName,
            'country' => $this->faker->countryCode,
            'type' => 'youtube',
            'image_url' => fn (array $attributes) => "https://picsum.photos/seed/{$attributes['uuid']}/160",
            'image_url_lg' => fn (array $attributes) => "https://picsum.photos/seed/{$attributes['uuid']}/512",
            'published_at' => now(),
        ];
    }
}
