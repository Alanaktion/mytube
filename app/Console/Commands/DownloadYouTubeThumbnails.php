<?php

namespace App\Console\Commands;

use App\Models\Video;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * @deprecated
 */
class DownloadYouTubeThumbnails extends Command
{
    /**
     * @var string
     */
    protected $signature = 'youtube:download-thumbnails {--G|generate : Generate unavailable thumbnails from video}';

    /**
     * @var string
     */
    protected $description = 'Download missing YouTube thumbnails.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('This command is deprecated as thumbnail download is now part of video import.');

        $videos = Video::where('source_type', 'youtube')
            ->where(function ($query): void {
                $query
                    ->whereNull('thumbnail_url')
                    ->orWhereNull('poster_url');
            })
            ->cursor();

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
                if ($this->option('generate')) {
                    try {
                        if ($this->generateImage($video)) {
                            if (!$video->thumbnail_url) {
                                $video->thumbnail_url = Storage::url("public/thumbs/generated/{$video->uuid}@360p.jpg");
                            }
                            if (!$video->poster_url) {
                                $video->poster_url = Storage::url("public/thumbs/generated/{$video->uuid}@720p.jpg");
                            }
                            if ($video->isDirty()) {
                                $video->save();
                            }
                        }
                    } catch (Exception $e2) {
                        Log::warning("Error generating thumbnail {$video->uuid}: {$e2->getMessage()}");
                    }
                } else {
                    Log::warning("Error downloading thumbnail {$video->uuid}: {$e->getMessage()}");
                }
            }
        }
        $bar->finish();
        $this->line('');

        return Command::SUCCESS;
    }

    protected function downloadThumbnail(string $uuid): void
    {
        $disk = Storage::disk('public');

        if (!$disk->exists("thumbs/youtube/{$uuid}.jpg")) {
            $data = file_get_contents("https://img.youtube.com/vi/{$uuid}/hqdefault.jpg");
            if ($data === false) {
                return;
            }
            $disk->put("thumbs/youtube/{$uuid}.jpg", $data, 'public');
        }

        // TODO: cleanly handle missing maxres but present thumbnail
        if (!$disk->exists("thumbs/youtube-maxres/{$uuid}.jpg")) {
            $data = file_get_contents("https://img.youtube.com/vi/{$uuid}/maxresdefault.jpg");
            if ($data === false) {
                return;
            }
            $disk->put("thumbs/youtube-maxres/{$uuid}.jpg", $data, 'public');
        }

        usleep(250e3);
    }

    /**
     * Generate images from a video's local file using ffmpeg
     */
    protected function generateImage(Video $video): bool
    {
        if (is_file(storage_path("app/public/thumbs/generated/{$video->uuid}@360p.jpg"))) {
            return true;
        }

        $video->load('files:id,video_id,path');
        if ($video->files->isEmpty()) {
            return false;
        }
        /** @var \App\Models\VideoFile $file */
        $file = $video->files->first();
        if (!is_file($file->path)) {
            return false;
        }

        // Determine video duration
        $path = escapeshellarg($file->path);
        $duration = shell_exec("ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $path 2>/dev/null");
        if ($duration) {
            $duration = (float)trim($duration);
        }

        // Seek to 30% of video duration, or 10 seconds if duration is unknown.
        $seconds = $duration ? floor($duration * 0.30) : 10;
        $framePath = storage_path("app/{$video->uuid}-frame.png");
        shell_exec("ffmpeg -ss $seconds -i $path -vframes 1 -vcodec png -an -y " . escapeshellarg($framePath) . " 2>/dev/null");

        Storage::makeDirectory('public/thumbs/generated');

        // Generate resampled + cropped images
        $thumb = Image::make($framePath)->resize(480, 360, function ($constraint): void {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->resizeCanvas(480, 360, 'center', false, '#000');
        $thumb->save(storage_path("app/public/thumbs/generated/{$video->uuid}@360p.jpg"));

        $thumb = Image::make($framePath)->resize(1280, 720, function ($constraint): void {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->resizeCanvas(1280, 720, 'center', false, '#000');
        $thumb->save(storage_path("app/public/thumbs/generated/{$video->uuid}@720p.jpg"));

        unlink($framePath);

        return true;
    }
}
