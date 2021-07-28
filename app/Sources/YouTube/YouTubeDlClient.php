<?php

namespace App\Sources\YouTube;

use YoutubeDl\YoutubeDl;

class YouTubeDlClient extends YoutubeDl
{
    public function getVersion(): ?string
    {
        try {
            $process = $this->createProcess([
                '--version',
            ]);
            $process->mustRun(is_callable($this->debug) ? $this->debug : null);
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
        $process = $this->createProcess([
            $url,
            '--flat-playlist',
            '--get-id',
        ]);

        $process->mustRun(is_callable($this->debug) ? $this->debug : null);
        return array_map('trim', explode("\n", trim($process->getOutput())));
    }
}
