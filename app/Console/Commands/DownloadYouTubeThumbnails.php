<?php

namespace App\Console\Commands;

use App\Models\Video;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadYouTubeThumbnails extends Command
{
    protected $signature = 'youtube:download-thumbnails';

    protected $description = 'Download missing YouTube thumbnails.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $videos = Video::whereHas('channel', function ($query) {
            $query->where('type', 'youtube');
        })->get();

        $bar = $this->output->createProgressBar($videos->count());
        $bar->start();
        foreach ($videos as $video) {
            $bar->advance();
            if ($this->hasThumbnail($video->uuid)) {
                continue;
            }
            try {
                $this->downloadThumbnail($video->uuid);
            } catch (Exception $e) {
                Log::warning("Error downloading thumbnail {$video->uuid}: {$e->getMessage()}");
            }
        }
        $bar->finish();
        $this->line('');
    }

    protected function hasThumbnail(string $uuid): bool
    {
        return Storage::disk('public')->exists("thumbs/youtube/{$uuid}.jpg");
    }

    protected function downloadThumbnail(string $uuid)
    {
        $data = file_get_contents("https://img.youtube.com/vi/{$uuid}/maxresdefault.jpg");
        Storage::disk('public')->put("thumbs/youtube/{$uuid}.jpg", $data, 'public');
        usleep(2e5);
    }
}
