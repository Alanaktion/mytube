<?php

namespace App\Console\Commands;

use App\Models\Playlist;
use Google_Service_YouTube;
use Illuminate\Console\Command;

/**
 * @deprecated
 */
class ImportYouTubePlaylist extends Command
{
    /**
     * @var string
     */
    protected $signature = 'youtube:import-playlist {id*}';

    /**
     * @var string
     */
    protected $description = 'Import YouTube playlists by ID.';

    public function __construct(protected Google_Service_YouTube $youtube)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('This command is deprecated, use import:playlist-url --source=youtube');

        $ids = $this->argument('id');

        foreach ($ids as $id) {
            if (str_contains($id, '/')) {
                $query = parse_url($id, PHP_URL_QUERY);
                parse_str($query, $parts);
                if (!empty($parts['list'])) {
                    $id = $parts['list'];
                }
            }
            if (!preg_match('/^PL[0-9a-z_-]{16,32}$/i', $id)) {
                $this->error('Invalid ID ' . $id);
                continue;
            }

            $this->info($id);
            Playlist::import('youtube', $id);
        }

        return 0;
    }
}
