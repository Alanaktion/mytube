<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;

class ImportUrl extends Command
{
    protected $signature = 'import:url {--source=} {url*}';

    protected $description = 'Import videos by URL.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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
                    if ($id = $source->matchUrl($url)) {
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
                $id = $typeSource->matchUrl($url);
            }
            if ($type === null) {
                $this->error('Unable to find source for URL: ' . $url);
            }
            if ($id === null) {
                $this->warn('Unable to match URL: ' . $url);
            }
            Video::import($type, $id);
        }

        return 0;
    }
}
