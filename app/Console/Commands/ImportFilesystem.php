<?php

namespace App\Console\Commands;

use App\Models\ImportError;
use App\Models\Video;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Output\OutputInterface;

class ImportFilesystem extends Command
{
    /**
     * @var string
     */
    protected $signature = 'import:filesystem
        {--source= : Which source site to import metadata from}
        {--r|retry : Retry previously failed imports}
        {directory : The directory to scan for video files}';

    /**
     * @var string
     */
    protected $description = 'Import videos from the filesystem.';

    /**
     * @var \App\Sources\Source[]
     */
    protected $sources = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $verbosity = $this->getOutput()->getVerbosity();
        $directory = $this->argument('directory');
        if (!is_dir($directory)) {
            $this->error('The specified directory does not exist.');
            return 1;
        }

        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            $this->sources[$source->getSourceType()] = $source;
        }

        $type = $this->option('source');
        if ($type && !isset($this->sources[$type])) {
            $this->error('Unable to find source type: ' . $type);
            return 2;
        }

        $this->line('Scanning directory...');
        $files = $this->getDirectoryFiles($directory);
        $videos = [];
        foreach ($files as $file) {
            $this->line($file, null, 'vv');
            $type = $this->option('source');
            $id = null;
            if ($type === null) {
                // Auto-detect source type from file
                foreach ($this->sources as $key => $source) {
                    /** @var \App\Sources\Source $source */
                    $id = $source->video()->matchFilename(basename($file));
                    if ($id !== null) {
                        $videos[] = [
                            'type' => $key,
                            'id' => $id,
                            'file' => $file,
                        ];
                        $this->line("Matched $key ID $id", null, 'vvv');
                    }
                }
            } else {
                $id = $this->sources[$type]->video()->matchFilename(basename($file));
                if ($id !== null) {
                    $videos[] = [
                        'type' => $type,
                        'id' => $id,
                        'file' => $file,
                    ];
                    $this->line("Matched $type ID $id", null, 'vvv');
                }
            }
            if ($id === null && $verbosity >= OutputInterface::VERBOSITY_VERBOSE) {
                $this->warn('Unable to find source for file: ' . $file);
            }
        }

        $videoCount = count($videos);
        $this->info("Importing $videoCount videos...");

        $errorCount = 0;

        $this->withProgressBar($videos, function (array $video) use (&$errorCount): void {
            try {
                if (!$this->option('retry')) {
                    $prevError = ImportError::where('uuid', $video['id'])
                        ->where('type', $video['type'])
                        ->exists();
                    if ($prevError) {
                        return;
                    }
                }
                Video::import($video['type'], $video['id'], $video['file']);
            } catch (Exception $e) {
                $errorCount++;
                if ($e->getMessage() != 'Video previously failed to import') {
                    ImportError::updateOrCreate([
                        'uuid' => $video['id'],
                        'type' => $video['type'],
                    ], [
                        'file_path' => $video['file'],
                        'reason' => $e->getMessage(),
                    ]);
                }
                Log::warning("Error importing file {$video['file']}: {$e->getMessage()}");
            }
        });
        $this->line('');

        if ($errorCount) {
            $this->error("Encountered errors importing $errorCount files.");
            return 3;
        }

        return 0;
    }

    /**
     * Retrieve and store video metadata if it doesn't already exist
     */
    protected function importVideo(
        string $type,
        string $id,
        string $filePath
    ): Video {
        $id = $this->sources[$type]->video()->canonicalizeId($id);
        if (ImportError::where('uuid', $id)->first()) {
            throw new Exception('Video previously failed to import');
        }

        return Video::import($type, $id, $filePath);
    }

    /**
     * Get all of the files in a directory recursively.
     *
     * This returns an array of all files in a directory, with complete paths.
     * @return string[]
     */
    protected function getDirectoryFiles(string $directory): array
    {
        $files = [];
        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $directory,
                RecursiveDirectoryIterator::FOLLOW_SYMLINKS
            ),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        foreach ($rii as $file) {
            /** @var \SplFileInfo $file */
            if ($file->isDir()) {
                continue;
            }
            $path = $file->getRealPath();
            if ($path !== false) {
                $files[] = $path;
            }
        }
        return $files;
    }
}
