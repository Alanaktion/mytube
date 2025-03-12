<?php

namespace App\Console\Commands;

use App\Models\ImportError;
use App\Models\Video;
use App\Traits\FilesystemHelpers;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Output\OutputInterface;

class ImportFilesystem extends Command
{
    use FilesystemHelpers;

    /**
     * @var string
     */
    protected $signature = 'import:filesystem
        {--source= : Which source site to import metadata from}
        {--exclude=* : Exclude paths matching the given pattern}
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
            return Command::INVALID;
        }

        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            $this->sources[$source->getSourceType()] = $source;
        }

        $type = $this->option('source');
        if ($type && !isset($this->sources[$type])) {
            $this->error('Unable to find source type: ' . $type);
            return Command::INVALID;
        }

        $this->line('Scanning directory...');
        $files = $this->getDirectoryFiles($directory);
        $videos = [];
        foreach ($files as $file) {
            $this->line($file, null, 'vv');
            if ($this->option('exclude')) {
                foreach ($this->option('exclude') as $pattern) {
                    if (!str_contains((string) $pattern, '*')) {
                        $pattern = "*{$pattern}*";
                    }
                    if (fnmatch($pattern, $file)) {
                        $this->line('Excluded by pattern: ' . $pattern, null, 'vv');
                        return Command::INVALID;
                    }
                }
            }
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
                $id = $this->sources[$video['type']]->video()->canonicalizeId($video['id']);
                Video::import($video['type'], $id, $video['file']);
            } catch (Exception $e) {
                $errorCount++;
                ImportError::updateOrCreate([
                    'uuid' => $video['id'],
                    'type' => $video['type'],
                ], [
                    'file_path' => $video['file'],
                    'reason' => $e->getMessage(),
                ]);
                Log::warning("Error importing file {$video['file']}: {$e->getMessage()}");
            }
        });
        $this->line('');

        if ($errorCount !== 0) {
            $this->error("Encountered errors importing $errorCount files.");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
