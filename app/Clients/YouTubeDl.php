<?php

namespace App\Clients;

use YoutubeDl\YoutubeDl as YTDLBase;

class YouTubeDl extends YTDLBase
{
    public function getVersion(): ?string
    {
        $process = $this->createProcess([
            '--version',
        ]);

        try {
            $process->mustRun(is_callable($this->debug) ? $this->debug : null);
            return $process->getOutput();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getChannelVideoIds(string $channelId): array
    {
        return $this->getIdsByUrl("https://www.youtube.com/c/${channelId}/videos");
    }

    public function getChannelPlaylistIds(string $channelId): array
    {
        return $this->getIdsByUrl("https://www.youtube.com/c/${channelId}/playlists");
    }

    protected function getIdsByUrl(string $url): array
    {
        $process = $this->createProcess([
            $url,
            '--flat-playlist',
            '--get-id',
        ]);

        $process->mustRun(is_callable($this->debug) ? $this->debug : null);
        return array_map('trim', explode("\n", $process->getOutput()));
    }
}
