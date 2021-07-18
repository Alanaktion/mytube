<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;

/**
 * @deprecated
 */
class ImportFloatplaneId extends Command
{
    protected $signature = 'floatplane:import {id*}';

    protected $description = 'Import Floatplane videos by ID.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->warn('This command is deprecated, use import:url --source=floatplane');

        $ids = $this->argument('id');

        foreach ($ids as $id) {
            if (str_contains($id, '/')) {
                $id = basename($id);
            }
            if (!preg_match('/^[0-9a-z_-]{10}$/i', $id)) {
                $this->error('Invalid ID ' . $id);
                continue;
            }

            $this->info($id);
            Video::importFloatplane($id);
        }

        return 0;
    }
}
