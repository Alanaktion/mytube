<?php

namespace App\Console\Commands;

use App\Models\Channel;
use App\Models\ImportError;
use App\Models\Video;
use App\Traits\FilesystemHelpers;
use Carbon\Carbon;
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
                            'info' => $this->findSidecarInfoJsonFile($file, $id),
                            'thumb' => $this->findSidecarImageFile($file),
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
                        'info' => $this->findSidecarInfoJsonFile($file, $id),
                        'thumb' => $this->findSidecarImageFile($file),
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
                $imported = $this->importUsingLocalMetadata(
                    $video['type'],
                    $id,
                    $video['file'],
                    $video['info'] ?? null
                );

                if ($imported === null) {
                    $imported = Video::import($video['type'], $id, $video['file']);
                }

                if (isset($video['thumb']) && $video['thumb']) {
                    $url = $this->copyThumbnailImage($video['thumb']);
                    $imported->update([
                        'thumbnail_url' => $url,
                        'poster_url' => $url,
                    ]);
                }
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

    /**
     * Import a video from local sidecar metadata.
     */
    protected function importUsingLocalMetadata(string $type, string $id, string $filePath, ?string $infoPath): ?Video
    {
        if (!$infoPath || !is_file($infoPath)) {
            return null;
        }

        $json = file_get_contents($infoPath);
        if ($json === false) {
            return null;
        }

        $info = json_decode($json, true);
        if (!is_array($info)) {
            return null;
        }

        $channelId = (string) ($info['channel_id'] ?? '');
        if ($channelId === '') {
            return null;
        }

        $channel = Channel::firstOrCreate(
            [
                'uuid' => $channelId,
                'type' => $type,
            ],
            [
                'title' => (string) ($info['channel'] ?? $info['uploader'] ?? 'Unknown Channel'),
                'description' => '',
                'custom_url' => $this->extractChannelCustomUrl($info),
                'published_at' => $this->extractPublishedAt($info),
                'metadata' => [
                    'uploader_id' => $info['uploader_id'] ?? null,
                    'uploader_url' => $info['uploader_url'] ?? null,
                ],
            ]
        );

        $video = Video::where('uuid', $id)
            ->whereHas('channel', function ($query) use ($type): void {
                $query->where('type', $type);
            })
            ->first();

        $videoData = [
            'channel_id' => $channel->id,
            'title' => (string) ($info['title'] ?? $id),
            'description' => (string) ($info['description'] ?? ''),
            'source_type' => $type,
            'source_visibility' => $this->normalizeVisibility((string) ($info['availability'] ?? 'public')),
            'duration' => isset($info['duration']) ? (int) $info['duration'] : null,
            'is_livestream' => (bool) ($info['is_live'] ?? false),
            'published_at' => $this->extractPublishedAt($info),
        ];

        if ($video === null) {
            $video = $channel->videos()->create(array_merge(['uuid' => $id], $videoData));
        } else {
            $video->update($videoData);
        }

        $video->addFile($filePath);

        ImportError::where('uuid', $id)
            ->where('type', $type)
            ->delete();

        return $video;
    }

    /**
     * @param array<string,mixed> $info
     */
    protected function extractPublishedAt(array $info): ?Carbon
    {
        if (isset($info['timestamp']) && is_numeric($info['timestamp'])) {
            return Carbon::createFromTimestamp((int) $info['timestamp']);
        }

        if (
            isset($info['upload_date'])
            && is_string($info['upload_date'])
            && preg_match('/^\d{8}$/', $info['upload_date'])
        ) {
            return Carbon::createFromFormat('Ymd', $info['upload_date']) ?: null;
        }

        return null;
    }

    /**
     * @param array<string,mixed> $info
     */
    protected function extractChannelCustomUrl(array $info): ?string
    {
        $uploaderId = $info['uploader_id'] ?? null;
        if (is_string($uploaderId) && $uploaderId !== '') {
            return ltrim($uploaderId, '@');
        }

        $channelUrl = $info['channel_url'] ?? null;
        if (is_string($channelUrl) && $channelUrl !== '') {
            $parts = parse_url($channelUrl);
            if (!isset($parts['path'])) {
                return null;
            }
            $segments = array_values(array_filter(explode('/', trim($parts['path'], '/'))));
            if ($segments === []) {
                return null;
            }
            return ltrim((string) end($segments), '@');
        }

        return null;
    }

    protected function normalizeVisibility(string $visibility): string
    {
        $visibility = strtolower($visibility);
        return match ($visibility) {
            Video::VISIBILITY_PRIVATE => Video::VISIBILITY_PRIVATE,
            Video::VISIBILITY_UNLISTED => Video::VISIBILITY_UNLISTED,
            default => Video::VISIBILITY_PUBLIC,
        };
    }
}
