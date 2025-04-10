<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Models\VideoFile;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class GenerateThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:thumbs {uuid?* : Specific video UUIDs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing thumbnails and poster images';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $query = Video::where('source_type', 'youtube')
            ->where(function ($query): void {
                $query
                    ->whereNull('thumbnail_url')
                    ->orWhereNull('poster_url');
            })
            ->with('files:id,video_id,path')
            ->whereHas('files');
        if ($this->argument('uuid')) {
            $query->whereIn('uuid', $this->argument('uuid'));
        }

        $videos = $query->cursor();

        $bar = $this->output->createProgressBar($videos->count());
        $bar->start();
        $errors = 0;
        foreach ($videos as $video) {
            $bar->advance();
            try {
                if ($this->generateImages($video, $video->files->first())) {
                    if (!$video->thumbnail_url) {
                        $video->thumbnail_url = Storage::url("public/thumbs/generated/{$video->uuid}@360p.jpg");
                    }
                    if (!$video->poster_url) {
                        $video->poster_url = Storage::url("public/thumbs/generated/{$video->uuid}@720p.jpg");
                    }
                    if ($video->isDirty()) {
                        $video->save();
                    }
                } else {
                    Log::warning("Unable to access video file to generate thumbnail {$video->uuid}");
                    $errors++;
                }
            } catch (Exception $e2) {
                Log::warning("Error generating thumbnail {$video->uuid}: {$e2->getMessage()}");
                $errors++;
            }
        }
        $bar->finish();
        $this->line('');
        $this->info("Generated {$bar->getMaxSteps()} thumbnails with {$errors} errors");

        return Command::SUCCESS;
    }

    /**
     * Generate images from a video's local file using ffmpeg
     */
    protected function generateImages(Video $video, VideoFile $file): bool
    {
        if (is_file(storage_path("app/public/thumbs/generated/{$video->uuid}@360p.jpg"))) {
            return true;
        }

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
        $manager = new ImageManager(
            new \Intervention\Image\Drivers\Gd\Driver()
        );
        $manager->read($framePath)
            ->scale(480, 360)
            ->resizeCanvas(480, 360, '#000')
            ->save(storage_path("app/public/thumbs/generated/{$video->uuid}@360p.jpg"));
        $manager->read($framePath)
            ->scale(1280, 720)
            ->resizeCanvas(1280, 720, '#000')
            ->save(storage_path("app/public/thumbs/generated/{$video->uuid}@720p.jpg"));

        unlink($framePath);

        return true;
    }
}
