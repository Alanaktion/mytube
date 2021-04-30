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
     *
     * @return int
     */
    public function handle()
    {
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
