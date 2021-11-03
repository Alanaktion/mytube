<?php

namespace App\Sources\YouTube;

use YoutubeDl\Process\DefaultProcessBuilder;
use YoutubeDl\YoutubeDl;

class YouTubeDlClient extends YoutubeDl
{
    public function getVersion(): ?string
    {
        // It'd be great if I could use the existing instance but it's private.
        $pb = new DefaultProcessBuilder();
        try {
            $process = $pb->build(null, null, [
                '--version',
            ]);
            // @phpstan-ignore-next-line
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
        // It'd be great if I could use the existing instance but it's private.
        $pb = new DefaultProcessBuilder();
        $process = $pb->build(null, null, [
            $url,
            '--flat-playlist',
            '--get-id',
        ]);

        // @phpstan-ignore-next-line
        $process->mustRun(is_callable($this->debug) ? $this->debug : null);
        return array_map('trim', explode("\n", trim($process->getOutput())));
    }
}
