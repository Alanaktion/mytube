<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Models\VideoFile;
use App\Traits\FilesystemHelpers;
use Illuminate\Console\Command;

class ImportFilesInteractive extends Command
{
    use FilesystemHelpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:attach-interactive
        {--source= : Limit file matching to specific source type}
        {directory : The directory to scan for video files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach unhandled files to videos interactively.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $directory = $this->argument('directory');
        if (!is_dir($directory)) {
            $this->error('The specified directory does not exist.');
            return 1;
        }

        $this->line('Scanning directory...');
        $files = $this->getDirectoryFiles($directory);
        foreach ($files as $file) {
            // Skip non-video files
            if (!in_array(pathinfo($file, PATHINFO_EXTENSION), ['mp4', 'mov', 'm4v', 'avi', 'mkv'])) {
                continue;
            }

            // Skip already-imported files
            if (VideoFile::where('path', $file)->exists()) {
                continue;
            }

            // Start manual import
            $this->import($file);
        }

        return 0;
    }

    protected function import(string $file): void
    {
        $pathinfo = pathinfo($file);
        $this->line($pathinfo['dirname'] . '/');
        $this->info($pathinfo['basename']);

        // Confirm desire to import file
        if (!$this->confirm('Import file?', true)) {
            return;
        }

        $type = $this->option('source');
        do {
            $videoTitle = $this->anticipate('Video title', function ($input) use ($type): array {
                if ($input == '') {
                    return [];
                }
                return Video::where('title', 'like', "$input%")
                    ->where('source_type', 'like', $type ?? '%')
                    ->pluck('title')
                    ->toArray();
            });
            $video = Video::where('title', $videoTitle)
                ->where('source_type', 'like', $type ?? '%')
                ->first();

            if ($video) {
                if (!$this->confirm("Use existing video: [{$video->uuid}] {$video->title}?", true)) {
                    $video = null;
                }
            }
        } while (!$video);

        /** @var Video $video */
        $videoFile = $video->addFile($file);

        $this->info('File added, id: ' . $videoFile->id);
        $this->line('');
    }
}
