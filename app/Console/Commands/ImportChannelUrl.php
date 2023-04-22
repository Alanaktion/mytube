<?php

namespace App\Console\Commands;

use App\Models\Channel;
use Exception;
use Illuminate\Console\Command;

class ImportChannelUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:channel-url
        {--source=}
        {--playlists}
        {--channels}
        {--videos}
        {url*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import channels by URL.';

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
                    if ($id = $source->channel()->matchUrl($url)) {
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
            if ($type === null || $typeSource === null) {
                $this->error('Unable to find source for URL: ' . $url);
                continue;
            } else {
                $id = $typeSource->playlist()->matchUrl($url);
            }
            if ($id === null) {
                $this->warn('Unable to match URL: ' . $url);
                continue;
            }

            $channel = Channel::import($type, $id);
            try {
                if ($this->option('playlists')) {
                    $channel->importPlaylists();
                }
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
            try {
                if ($this->option('videos')) {
                    $channel->importVideos();
                }
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
