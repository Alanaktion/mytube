<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportSources extends Command
{
    protected $signature = 'import:sources';

    protected $description = 'List available sources to import from.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * @var \App\Sources\Source[] $sources
         */
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            $this->line($source->getSourceType());
        }
    }
}
