<?php

namespace App\Sources\YtDlp;

use App\Exceptions\ImportException;
use Carbon\Carbon;
use Symfony\Component\Process\ExecutableFinder;
use YoutubeDl\Process\DefaultProcessBuilder;
use YoutubeDl\Process\ProcessBuilderInterface;
use YoutubeDl\YoutubeDl;

class YtDlpClient extends YoutubeDl
{
    protected ProcessBuilderInterface $processBuilder;
    protected ?string $binPath;
    protected bool $versionResolved = false;
    protected ?string $version = null;

    public function __construct(?string $binPath = null)
    {
        $executableFinder = new ExecutableFinder();
        $this->processBuilder = new DefaultProcessBuilder($executableFinder);
        parent::__construct($this->processBuilder);

        $configuredPath = config('app.ytdl.path');
        $this->binPath = $binPath
            ?? $configuredPath
            ?? $executableFinder->find('yt-dlp')
            ?? $executableFinder->find('youtube-dl');
        if ($this->binPath !== null) {
            $this->setBinPath($this->binPath);
        }
    }

    public function isAvailable(): bool
    {
        return $this->getVersion() !== null;
    }

    public function getVersion(): ?string
    {
        if ($this->versionResolved) {
            return $this->version;
        }

        $this->versionResolved = true;
        if ($this->binPath === null) {
            return null;
        }

        try {
            $this->version = trim($this->run([
                '--version',
            ]));
        } catch (ImportException) {
            $this->version = null;
        }

        return $this->version;
    }

    /**
     * @return array<string, mixed>
     */
    public function getVideoMetadata(string $url): array
    {
        return $this->getJsonByUrl($url, [
            '--dump-single-json',
            '--skip-download',
            '--no-warnings',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getChannelMetadata(string $url): array
    {
        return $this->getJsonByUrl($url, [
            '--dump-single-json',
            '--skip-download',
            '--flat-playlist',
            '--playlist-end',
            '1',
            '--no-warnings',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getPlaylistMetadata(string $url): array
    {
        return $this->getJsonByUrl($url, [
            '--dump-single-json',
            '--skip-download',
            '--flat-playlist',
            '--playlist-end',
            '1',
            '--no-warnings',
        ]);
    }

    /**
     * @return string[]
     */
    public function getPlaylistVideoIds(string $url): array
    {
        return $this->getIdsByUrl($url);
    }

    /**
     * @return string[]
     */
    protected function getIdsByUrl(string $url): array
    {
        $output = $this->run([
            $url,
            '--flat-playlist',
            '--get-id',
            '--no-warnings',
        ]);

        return array_values(array_filter(array_map('trim', explode("\n", trim($output)))));
    }

    public static function getPublishedAt(array $data): ?Carbon
    {
        foreach (['timestamp', 'release_timestamp'] as $field) {
            if (isset($data[$field]) && is_numeric($data[$field])) {
                return Carbon::createFromTimestamp((int) $data[$field]);
            }
        }

        foreach (['upload_date', 'release_date'] as $field) {
            if (!empty($data[$field]) && is_string($data[$field])) {
                try {
                    return Carbon::createFromFormat('Ymd', $data[$field])->startOfDay();
                } catch (\Exception) {
                    continue;
                }
            }
        }

        return null;
    }

    public static function getThumbnailUrl(array $data, ?int $minimumWidth = null): ?string
    {
        $thumbnails = $data['thumbnails'] ?? null;
        if (is_array($thumbnails)) {
            $candidates = array_values(array_filter($thumbnails, function ($thumbnail): bool {
                return is_array($thumbnail) && !empty($thumbnail['url']) && is_string($thumbnail['url']);
            }));

            usort($candidates, function (array $left, array $right): int {
                $leftWidth = (int) ($left['width'] ?? 0);
                $rightWidth = (int) ($right['width'] ?? 0);
                return $leftWidth <=> $rightWidth;
            });

            if ($minimumWidth !== null) {
                foreach ($candidates as $thumbnail) {
                    if ((int) ($thumbnail['width'] ?? 0) >= $minimumWidth) {
                        return $thumbnail['url'];
                    }
                }
            }

            if ($candidates !== []) {
                return $candidates[count($candidates) - 1]['url'];
            }
        }

        $thumbnail = $data['thumbnail'] ?? null;
        return is_string($thumbnail) && $thumbnail !== '' ? $thumbnail : null;
    }

    /**
     * @param string[] $arguments
     * @return array<string, mixed>
     */
    protected function getJsonByUrl(string $url, array $arguments): array
    {
        $output = $this->run([
            ...$arguments,
            $url,
        ]);

        try {
            $data = json_decode($output, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException('yt-dlp returned invalid JSON: ' . $e->getMessage(), previous: $e);
        }

        if (!is_array($data)) {
            throw new ImportException('yt-dlp returned an unexpected response.');
        }

        return $data;
    }

    /**
     * @param string[] $arguments
     */
    protected function run(array $arguments): string
    {
        if ($this->binPath === null) {
            throw new ImportException('yt-dlp is not installed or configured.');
        }

        $process = $this->processBuilder->build($this->binPath, null, $arguments);
        $process->run();
        if (!$process->isSuccessful()) {
            $message = trim($process->getErrorOutput()) ?: trim($process->getOutput()) ?: 'yt-dlp request failed.';
            throw new ImportException($message);
        }

        return $process->getOutput();
    }
}
