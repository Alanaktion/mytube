<?php

namespace App\Console\Commands;

use App\Models\ImportError;
use App\Models\Video;
use Exception;
use Google_Service_YouTube;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Output\OutputInterface;

class ImportYouTube extends Command
{
    protected $signature = 'youtube:import-fs {directory}';

    protected $description = 'Import YouTube videos from the filesystem.';

    protected $youtube;

    public function __construct(Google_Service_YouTube $youtube)
    {
        parent::__construct();
        $this->youtube = $youtube;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $verbosity = $this->getOutput()->getVerbosity();
        $directory = $this->argument('directory');
        if (!is_dir($directory)) {
            $this->error('The specified directory does not exist.');
            return 1;
        }

        $this->line('Scanning directory...');
        $files = $this->getDirectoryFiles($directory);
        $videos = [];
        foreach ($files as $file) {
            // Match filename pattern from youtube-dl
            // Long-term this should be made better somehow
            if (preg_match('/-([A-Za-z0-9_\-]{11})\.(mp4|m4v|avi|mkv|webm)$/', $file, $matches)) {
                $videos[$matches[1]] = $file;
            } elseif ($verbosity >= OutputInterface::VERBOSITY_VERBOSE) {
                $this->warn('Unmatched file: ' . $file);
            }
        }

        $videoCount = count($videos);
        $this->info("Importing $videoCount videos...");

        $errorCount = 0;

        $bar = $this->output->createProgressBar($videoCount);
        $bar->start();
        foreach ($videos as $id => $file) {
            try {
                $this->importVideo($id, $file);
            } catch (Exception $e) {
                $errorCount++;
                if ($e->getMessage() != 'Video previously failed to import') {
                    ImportError::updateOrCreate([
                        'uuid' => $id,
                        'file_path' => $file,
                    ]);
                }
                Log::warning("Error importing file $file: {$e->getMessage()}");
            }
            $bar->advance();
        }
        $bar->finish();
        $this->line('');

        if ($errorCount) {
            $this->error("Encountered errors importing $errorCount files.");
            return 1;
        }

        return 0;
    }

    /**
     * Retrieve and store video metadata if it doesn't already exist
     */
    protected function importVideo(string $id, string $filePath): Video
    {
        $video = Video::where('uuid', $id)
            ->whereHas('channel', function ($query) {
                $query->where('type', 'youtube');
            })
            ->first();
        if ($video) {
            if ($video->file_path === null) {
                $video->file_path = $filePath;
                $video->save();
            }
            return $video;
        }

        if (ImportError::where('uuid', $id)->first()) {
            throw new Exception('Video previously failed to import');
        }

        return Video::importYouTube($id, $filePath);
    }

    protected function getDirectoryFiles(string $directory): array
    {
        $files = [];
        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $directory,
                RecursiveDirectoryIterator::KEY_AS_PATHNAME & RecursiveDirectoryIterator::FOLLOW_SYMLINKS
            ),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        foreach ($rii as $file) {
            /** @var \SplFileInfo $file */
            if ($file->isDir()) {
                continue;
            }
            $files[] = $file->getRealPath();
        }
        return $files;
    }
}
