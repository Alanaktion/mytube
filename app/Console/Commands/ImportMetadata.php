<?php

namespace App\Console\Commands;

use App\Models\VideoFile;
use Illuminate\Console\Command;

class ImportMetadata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:metadata
        {--F|no-ffprobe : Skip metadata that requires ffprobe}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import missing metadata for video files.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $files = VideoFile::whereNull('size');
        if (!$this->option('no-ffprobe')) {
            $files->whereNull('width');
        }
        if (!$files->exists()) {
            $this->info('No video files are missing metadata.');
            return Command::SUCCESS;
        }

        $this->withProgressBar($files->cursor(), function (VideoFile $file): void {
            if (!is_file($file->path)) {
                $this->warn('File does not exist: ' . $file->path);
                return;
            }
            if ($file->size === null) {
                $file->size = filesize($file->path);
            }
            if ($file->width === null && !$this->option('no-ffprobe')) {
                $meta = VideoFile::getMetadataForFile($file->path);
                if ($meta !== null) {
                    $file->fill($meta);
                }
            }

            if ($file->isDirty() && $file->exists) {
                $file->save();
            }
        });
        $this->line('');

        return Command::SUCCESS;
    }
}
