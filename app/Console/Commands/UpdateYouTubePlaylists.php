<?php

namespace App\Console\Commands;

use App\Models\Playlist;
use Exception;
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
        })->cursor();

        /** @var Playlist[] */
        $errors = [];

        $this->withProgressBar($playlists, function ($p) use ($errors) {
            try {
                Playlist::importYouTube($p->uuid);
            } catch (Exception $e) {
                $errors[] = $p;
            }
        });

        foreach ($errors as $e) {
            $this->error("Failed to update playlist {$e->uuid}");
        }

        return 0;
    }
}
