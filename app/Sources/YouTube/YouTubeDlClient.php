<?php

namespace App\Sources\YouTube;

use App\Sources\YtDlp\YtDlpClient;

class YouTubeDlClient extends YtDlpClient
{
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
        return $this->getIdsByUrl("https://www.youtube.com/channel/${channelId}/playlists?view=1");
    }
}
