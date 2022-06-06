<?php

namespace App\Sources\Twitch\Source;

use App\Models\Playlist;
use App\Sources\SourcePlaylist;

class TwitchCollection implements SourcePlaylist
{
    public function import(string $id): Playlist
    {
        throw new \Exception('Twitch collections cannot be imported automatically, they are not supported by the API.');
    }

    public function importItems(Playlist $playlist): void
    {
        throw new \Exception('Twitch collections cannot be imported automatically, they are not supported by the API.');
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https?:\/\/)?(www\.)?twitch\.tv\/collections\?([0-9a-z]{14,})$/i', $url, $matches)) {
            return $matches[3];
        }
        return null;
    }

    public function matchId(string $id): bool
    {
        return (bool)preg_match('/^[0-9a-z]{14,}$/i', $id);
    }

    public function getSourceUrl(Playlist $playlist): string
    {
        return 'https://www.twitch.tv/collections/' . $playlist->uuid;
    }
}
