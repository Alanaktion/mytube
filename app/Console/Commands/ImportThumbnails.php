<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Traits\FilesystemHelpers;
use Illuminate\Console\Command;

class ImportThumbnails extends Command
{
    use FilesystemHelpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:thumbnails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import thumbnail images from filesystem when missing from the database.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $videos = Video::whereHas('files')
            ->with('files:id,video_id,path')
            ->whereNull('thumbnail_url')
            ->orWhereNull('poster_url')
            ->cursor();

        if (!$videos->count()) {
            $this->info('No videos missing thumbnails with files available.');
            return Command::SUCCESS;
        }

        $this->withProgressBar($videos, function (Video $video, $bar): void {
            foreach ($video->files as $file) {
                $ext = pathinfo($file->path, PATHINFO_EXTENSION);
                $glob = substr($file->path, 0, -strlen($ext)) . '*';
                $imageFiles = glob($glob);
                if (!$imageFiles) {
                    continue;
                }
                foreach ($imageFiles as $path) {
                    $imageExt = pathinfo($path, PATHINFO_EXTENSION);
                    if (in_array($imageExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $url = $this->copyThumbnailImage($path);
                        $video->update([
                            'thumbnail_url' => $url,
                            'poster_url' => $url,
                        ]);
                        break 2;
                    }
                }
            }
        });
        $this->line('');

        return Command::SUCCESS;
    }
}
