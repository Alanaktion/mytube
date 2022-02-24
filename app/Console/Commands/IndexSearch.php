<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class IndexSearch extends Command
{
    protected const MODELS = [
        \App\Models\Video::class,
        \App\Models\Playlist::class,
        \App\Models\Channel::class,
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:index {--F|flush : Flush the indexes before importing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update search indexes to include new records';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $driver = config('scout.driver');
        if ($driver === null) {
            $this->warn('No search index driver is configured.');
            return 1;
        }
        if ($driver === 'database' || $driver === 'collection') {
            $this->warn("The search driver '$driver' does not require indexing.");
            return 1;
        }

        if ($this->option('flush')) {
            foreach (self::MODELS as $model) {
                $this->call('scout:flush', ['model' => $model]);
            }
        }

        foreach (self::MODELS as $model) {
            $this->call('scout:import', ['model' => $model]);
        }

        return 0;
    }
}
