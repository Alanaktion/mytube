<?php

namespace App\Console\Commands;

use App\Models\VideoFile;
use Illuminate\Console\Command;

class DeleteMissingFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:delete-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete file records for missing files.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deleted = 0;
        $this->withProgressBar(VideoFile::cursor(), function (VideoFile $file) use (&$deleted): void {
            if (!is_file($file->path)) {
                $file->delete();
                $deleted++;
            }
        });
        $this->line('');

        $this->line($deleted . ' records deleted.');

        return Command::SUCCESS;
    }
}
