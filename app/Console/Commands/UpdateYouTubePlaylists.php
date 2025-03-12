<?php

namespace App\Console\Commands;

use App\Models\Playlist;
use Exception;
use Illuminate\Console\Command;

/**
 * @todo Make a generalized version of this command for all sources.
 */
class UpdateYouTubePlaylists extends Command
{
    /**
     * @var string
     */
    protected $signature = 'youtube:update-playlists';

    /**
     * @var string
     */
    protected $description = 'Update YouTube playlists.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $playlists = Playlist::whereHas('channel', function ($query): void {
            $query->where('type', 'youtube');
        })->cursor();

        $errors = [];

        $this->withProgressBar($playlists, function ($p) use ($errors): void {
            try {
                Playlist::import('youtube', $p->uuid);
            } catch (Exception) {
                $errors[] = $p;
            }
        });

        foreach ($errors as $error) {
            $this->error("Failed to update playlist {$error->uuid}");
        }

        return Command::SUCCESS;
    }
}
