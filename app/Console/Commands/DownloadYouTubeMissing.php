<?php

namespace App\Console\Commands;

use App\Jobs\DownloadVideo;
use App\Models\Video;
use Illuminate\Console\Command;

/**
 * @todo Make a generalized version of this command for all sources.
 */
class DownloadYouTubeMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:download-videos {--queue : Whether to queue the downloads}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download any videos missing local files.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $videos = Video::whereNull('file_path')->cursor();
        if (!$videos->count()) {
            $this->info('No video are missing local files.');
            return 0;
        }

        $this->withProgressBar($videos, function (Video $video, $bar): void {
            if ($this->option('queue')) {
                // Queue the video download
                DownloadVideo::dispatch($video->uuid)->onQueue('download');
            } else {
                // Download the video
                $video->downloadVideo();
            }
        });
        $this->line('');

        if ($this->option('queue')) {
            $this->info(sprintf('Queued %u downloads.', $videos->count()));
        }

        return 0;
    }
}
