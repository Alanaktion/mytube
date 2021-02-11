<?php

namespace App\Console\Commands;

use App\Models\Playlist;
use Illuminate\Console\Command;

class UpdateYouTubePlaylists extends Command
{
    protected $signature = 'youtube:update-playlists';

    protected $description = 'Update YouTube playlists.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $playlists = Playlist::whereHas('channel', function ($query) {
            $query->where('type', 'youtube');
        })->get();

        $this->withProgressBar($playlists, function ($p) {
            Playlist::importYouTube($p->uuid);
        });

        return 0;
    }
}
