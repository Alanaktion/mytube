<?php

namespace App\Console\Commands;

use App\Models\Channel;
use App\Models\Video;
use App\Models\VideoFile;
use App\Traits\FilesystemHelpers;
use DateTime;
use Illuminate\Console\Command;

class ImportFilesInteractive extends Command
{
    use FilesystemHelpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:files-interactive
        {directory : The directory to scan for video files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import files interactively, manually matching channels and sources.';

    /**
     * @var \App\Sources\Source[]
     */
    protected $sources = [];

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

        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            $this->sources[$source->getSourceType()] = $source;
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

        // Select video source
        $sources = [];
        foreach ($this->sources as $key => $source) {
            /** @var \App\Sources\Source $source */
            $id = $source->video()->matchFilename(basename($file));
            if ($id !== null) {
                $sources[$key] = $id;
            }
        }

        // TODO: allow manually specifying source when not matched
        if (!$sources) {
            $this->error('No source matched this file.');
            $this->line('');
            return;
        }

        // Confirm desire to import file
        if (!$this->confirm('Import file?', true)) {
            return;
        }

        if (count($sources) > 1) {
            $source = $this->choice('Source:', array_keys($sources));
        } else {
            $source = array_keys($sources)[0];
            $this->line('Source: ' . $source);
        }

        $uuid = $this->ask('Video ID:', $sources[$source]);
        $title = $this->ask('Video title:', $pathinfo['filename']);

        do {
            $channel_title = $this->anticipate('Channel title:', function ($input) use ($source): array {
                if ($input == '') {
                    return [];
                }
                return Channel::where('type', $source)
                    ->where('title', 'like', "$input%")
                    ->pluck('title')
                    ->toArray();
            });
            $channel = Channel::where('type', $source)
                ->where('title', $channel_title)
                ->first();

            if ($channel) {
                if (!$this->confirm('Use existing channel: ' . $channel->title . '?', true)) {
                    $channel = null;
                }
            } else {
                $this->warn('Creating new channel with title: ' . $channel_title);
                $channel = Channel::create([
                    'type' => $source,
                    'title' => $channel_title,
                    'uuid' => $this->ask('Channel UUID:'),
                    'custom_url' => $this->ask('Channel URL name (optional):'),
                    'published_at' => new DateTime($this->ask('Channel published at:', 'now')),
                ]);
            }
        } while (!$channel);

        $visibility = $this->choice('Video visibility:', ['public', 'unlisted', 'private'], 'public');
        $is_livestream = $this->confirm('Is this a livestream?', false);

        /** @var Channel $channel */
        /** @var Video $video */
        $video = $channel->videos()->create([
            'uuid' => $uuid,
            'title' => $title,
            'description' => '',
            'source_type' => $source,
            'source_visibility' => $visibility,
            'is_livestream' => $is_livestream,
            'published_at' => new DateTime('@' . filemtime($file)),
        ]);
        $video->addFile($file);

        $this->info('Video imported, id: ' . $video->id);

        // Find thumbnail files, falling back to generated thumbnail prompt
        /** @see ImportThumbnails */
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $glob = substr($file, 0, -strlen($ext)) . '*';
        $imageFiles = glob($glob);
        if ($imageFiles) {
            foreach ($imageFiles as $path) {
                $imageExt = pathinfo($path, PATHINFO_EXTENSION);
                if (in_array($imageExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $this->info('Found local thumbnail image, using that.');
                    $url = $this->copyThumbnailImage($path);
                    $video->update([
                        'thumbnail_url' => $url,
                        'poster_url' => $url,
                    ]);
                    break;
                }
            }
        } else {
            if ($this->confirm('Generate thumbnails?', true)) {
                $this->call('generate:thumbs', ['uuid' => [$video->uuid]]);
            }
        }

        $this->line('');
    }
}
