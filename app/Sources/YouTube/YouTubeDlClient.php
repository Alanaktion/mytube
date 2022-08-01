<?php

namespace App\Sources\YouTube;

use Symfony\Component\Process\ExecutableFinder;
use YoutubeDl\Process\DefaultProcessBuilder;
use YoutubeDl\Process\ProcessBuilderInterface;
use YoutubeDl\YoutubeDl;

class YouTubeDlClient extends YoutubeDl
{
    protected ProcessBuilderInterface $processBuilder;
    protected string $binPath;

    public function __construct()
    {
        $executableFinder = new ExecutableFinder();
        $this->processBuilder = new DefaultProcessBuilder($executableFinder);
        parent::__construct($this->processBuilder);

        $binPath = config('app.ytdl.path');
        $this->binPath = $binPath ?? $executableFinder->find('yt-dlp') ?? $executableFinder->find('youtube-dl');
        $this->setBinPath($this->binPath);
    }

    public function getVersion(): ?string
    {
        try {
            $process = $this->processBuilder->build($this->binPath, null, [
                '--version',
            ]);
            $process->mustRun();
            return $process->getOutput();
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * @return string[]
     */
    public function getChannelVideoIds(string $channelId): array
    {
        return $this->getIdsByUrl("https://www.youtube.com/channel/${channelId}/videos");
    }

    /**
     * @return string[]
     */
    public function getChannelPlaylistIds(string $channelId): array
    {
        return $this->getIdsByUrl("https://www.youtube.com/channel/${channelId}/playlists");
    }

    /**
     * @return string[]
     */
    protected function getIdsByUrl(string $url): array
    {
        $process = $this->processBuilder->build($this->binPath, null, [
            $url,
            '--flat-playlist',
            '--get-id',
        ]);

        $process->mustRun();
        return array_map('trim', explode("\n", trim($process->getOutput())));
    }
}
