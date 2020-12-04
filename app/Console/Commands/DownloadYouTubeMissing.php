<?php

namespace App\Console\Commands;

use App\Jobs\DownloadVideo;
use App\Models\Video;
use Illuminate\Console\Command;

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
     *
     * @return int
     */
    public function handle()
    {
        $videos = Video::whereNull('file_path')->get();

        $this->withProgressBar($videos, function (Video $video, $bar) {
            if ($this->option('queue')) {
                // Queue the video download
                DownloadVideo::dispatch($video->uuid);
            } else {
                // Download the video
                $video->downloadVideo();
            }
        });

        return 0;
    }
}
