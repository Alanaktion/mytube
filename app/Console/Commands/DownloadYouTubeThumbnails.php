<?php

namespace App\Console\Commands;

use App\Models\Video;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DownloadYouTubeThumbnails extends Command
{
    protected $signature = 'youtube:download-thumbnails {--G|generate : Generate unavailable thumbnails from video}';

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
                if ($this->option('generate')) {
                    $this->generateImage($video);
                }
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

    /**
     * Generate images from a video's local file using ffmpeg
     */
    protected function generateImage(Video $video)
    {
        if (!$video->file_path || !is_file($video->file_path)) {
            return;
        }

        // Determine video duration
        $path = escapeshellarg($video->file_path);
        $duration = trim(shell_exec("ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $path"));

        // Seek to 30% of video duration, or 10 seconds if duration is unknown.
        $seconds = $duration ? floor($duration * 0.30) : 10;
        $framePath = storage_path("app/{$video->uuid}-frame.png");
        shell_exec("ffmpeg -ss $seconds -i $path -vframes 1 -vcodec png -an -y " . escapeshellarg($framePath));

        Storage::makeDirectory('public/thumbs/generated');

        // Generate resampled + cropped images
        $thumb = Image::make($framePath)->resize(480, 360, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->resizeCanvas(480, 360, 'center', false, '#000');
        $thumb->save(storage_path("app/public/thumbs/generated/{$video->uuid}@360p.jpg"));

        $thumb = Image::make($framePath)->resize(1280, 720, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->resizeCanvas(1280, 720, 'center', false, '#000');
        $thumb->save(storage_path("app/public/thumbs/generated/{$video->uuid}@720p.jpg"));

        unlink($framePath);

        return true;
    }
}
