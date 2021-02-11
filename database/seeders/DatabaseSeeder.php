<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Playlist;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Channel::factory()
            ->times(4)
            ->hasVideos(8)
            ->create();
        Playlist::factory()
            ->hasItems(3)
            ->create();
    }
}
