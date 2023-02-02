<?php

namespace App\Console\Commands;

use App\Models\Playlist;
use Illuminate\Console\Command;

class ImportPlaylistUrl extends Command
{
    /**
     * @var string
     */
    protected $signature = 'import:playlist-url
        {--source=}
        {--no-items : Don\'t import the items with the playlist}
        {url*}';

    /**
     * @var string
     */
    protected $description = 'Import playlists by URL.';

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
                    if (!$source->playlist()) {
                        continue;
                    }
                    if ($id = $source->playlist()->matchUrl($url)) {
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
            } elseif ($typeSource->playlist() === null) {
                $this->error('Source does not support playlists: ' . $type);
                return 1;
            } elseif ($id === null) {
                $id = $typeSource->playlist()->matchUrl($url);
            }
            if ($id === null) {
                $this->warn('Unable to match URL: ' . $url);
                continue;
            }

            Playlist::import($type, $id, !$this->option('no-items'));
        }

        return Command::SUCCESS;
    }
}
