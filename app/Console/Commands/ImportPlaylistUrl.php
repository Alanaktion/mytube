<?php

namespace App\Console\Commands;

use App\Models\Playlist;
use Illuminate\Console\Command;

class ImportPlaylistUrl extends Command
{
    protected $signature = 'import:playlist-url
        {--source=}
        {--items : Import the items with they playlist}
        {url*}';

    protected $description = 'Import playlists by URL.';

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
            }
            if ($type === null) {
                $this->error('Unable to find source for URL: ' . $url);
            } else {
                $id = $typeSource->matchUrl($url);
            }
            if ($id === null) {
                $this->warn('Unable to match URL: ' . $url);
            }
            if ($type !== null && $id !== null) {
                $playlist = Playlist::import($type, $id);

                if ($this->option('items')) {
                    $source->importPlaylistItems($playlist);
                }
            }
        }

        return 0;
    }
}
