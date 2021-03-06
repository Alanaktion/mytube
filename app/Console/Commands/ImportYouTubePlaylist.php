<?php

namespace App\Console\Commands;

use App\Models\Playlist;
use Google_Service_YouTube;
use Illuminate\Console\Command;

class ImportYouTubePlaylist extends Command
{
    protected $signature = 'youtube:import-playlist {id*}';

    protected $description = 'Import YouTube videos from the filesystem.';

    protected $youtube;

    public function __construct(Google_Service_YouTube $youtube)
    {
        parent::__construct();
        $this->youtube = $youtube;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
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
            Playlist::importYouTube($id);
        }

        return 0;
    }
}
