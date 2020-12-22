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
        })->where(function ($query) {
            $query->whereNull('thumbnail_url')
                ->orWhereNull('poster_url');
        })->get();

        $bar = $this->output->createProgressBar($videos->count());
        $bar->start();
        foreach ($videos as $video) {
            $bar->advance();
            try {
                $this->downloadThumbnail($video->uuid);
                if (!$video->thumbnail_url) {
                    $video->thumbnail_url = Storage::url("public/thumbs/youtube/{$video->uuid}.jpg");
                }
                if (!$video->poster_url) {
                    $video->poster_url = Storage::url("public/thumbs/youtube-maxres/{$video->uuid}.jpg");
                }
                if ($video->isDirty()) {
                    $video->save();
                }
            } catch (Exception $e) {
                Log::warning("Error downloading thumbnail {$video->uuid}: {$e->getMessage()}");
            }
        }
        $bar->finish();
        $this->line('');
    }

    protected function downloadThumbnail(string $uuid)
    {
        $disk = Storage::disk('public');

        if (!$disk->exists("thumbs/youtube/{$uuid}.jpg")) {
            $data = file_get_contents("https://img.youtube.com/vi/{$uuid}/hqdefault.jpg");
            $disk->put("thumbs/youtube/{$uuid}.jpg", $data, 'public');
        }

        if (!$disk->exists("thumbs/youtube-maxres/{$uuid}.jpg")) {
            $data = file_get_contents("https://img.youtube.com/vi/{$uuid}/maxresdefault.jpg");
            $disk->put("thumbs/youtube-maxres/{$uuid}.jpg", $data, 'public');
        }

        usleep(250e3);
    }
}
