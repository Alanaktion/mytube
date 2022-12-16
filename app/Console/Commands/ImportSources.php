<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportSources extends Command
{
    /**
     * @var string
     */
    protected $signature = 'import:sources';

    /**
     * @var string
     */
    protected $description = 'List available sources to import from.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /**
         * @var \App\Sources\Source[] $sources
         */
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            $this->line($source->getSourceType());
        }

        return Command::SUCCESS;
    }
}
