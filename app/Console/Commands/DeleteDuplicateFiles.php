<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;

class DeleteDuplicateFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:delete-duplicates
        {--m|mime-type= : Keep the specified mime-type}
        {--resolution= : Keep the specified resolution (height)}
        {--f|force : Delete files without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete duplicate video files interactively.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $videos = Video::withCount('files')
            ->having('files_count', '>', 1)
            ->with('files')
            ->get();

        $this->info('Found ' . $videos->count() . ' videos with duplicate files.');

        foreach ($videos as $video) {
            $choices = [];
            $default = null;
            $i = -1;
            foreach ($video->files as $file) {
                $type = strtoupper(pathinfo($file->path, PATHINFO_EXTENSION));
                $size = number_format($file->size / 1024 / 1024, 1) . ' MiB';
                $name = basename(dirname($file->path)) . '/' . basename($file->path);
                $choices[$i] = "$type $size - $name {$file->id}";
                $i++;
                if ($this->option('mime-type') && $file->mime_type != $this->option('mime-type')) {
                    continue;
                }
                if ($this->option('resolution') && $file->height != $this->option('resolution')) {
                    continue;
                }
                if ($this->option('mime-type') || $this->option('resolution')) {
                    $default = $i;
                }
            }
            $keepChoices = $this->choice(
                "Files to keep for: [{$video->uuid}] {$video->title}",
                $choices,
                default: $default,
                multiple: true,
            );
            $keepIds = array_map(
                function ($choice) {
                    preg_match('/\d+$/', $choice, $matches);
                    return $matches[0];
                },
                $keepChoices,
            );

            foreach ($video->files as $file) {
                if (in_array($file->id, $keepIds)) {
                    continue;
                }
                if (!$this->option('force') && !$this->confirm("Delete: {$file->path}")) {
                    continue;
                }
                $this->info("Deleting: {$file->path}");
                $file->delete();
            }
        }

        return Command::SUCCESS;
    }
}
