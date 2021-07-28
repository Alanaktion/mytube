<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;

class ImportUrl extends Command
{
    /**
     * @var string
     */
    protected $signature = 'import:url {--source=} {url*}';

    /**
     * @var string
     */
    protected $description = 'Import videos by URL.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $urls = $this->argument('url');

        $sources = app()->tagged('sources');

        foreach ($urls as $url) {
            $id = null;
            $type = $this->option('source');
            $typeSource = null;
            if ($type === null) {
                foreach ($sources as $source) {
                    /** @var \App\Sources\Source $source */
                    if ($id = $source->video()->matchUrl($url)) {
                        $type = $source->getSourceType();
                        $typeSource = $source;
                        break;
                    }
                }
            } else {
                foreach ($sources as $source) {
                    /** @var \App\Sources\Source $source */
                    if ($source->getSourceType() == $type) {
                        $typeSource = $source;
                        break;
                    }
                }
            }
            if ($type === null) {
                $this->error('Unable to find source for URL: ' . $url);
            } else {
                $id = $typeSource->video()->matchUrl($url);
            }
            if ($id === null) {
                $this->warn('Unable to match URL: ' . $url);
            }
            if ($type !== null && $id !== null) {
                Video::import($type, $id);
            }
        }

        return 0;
    }
}
