<?php

namespace App\Sources;

use App\Models\Playlist;

interface SourcePlaylist
{
    /**
     * Import a playlist from the source by ID.
     *
     * @api
     */
    public function import(string $id): Playlist;

    /**
     * Import items from a playlist, if supported by the source.
     *
     * @api
     */
    public function importItems(Playlist $playlist): void;

    /**
     * Determine if the given URL belongs to this source.
     *
     * @api
     *
     * @return string|null The ID of the playlist in the URL, or null if unmatched.
     */
    public function matchUrl(string $url): ?string;

    /**
     * Determine if the given playlist ID belongs to this source.
     *
     * @api
     */
    public function matchId(string $id): bool;

    /**
     * Get a source URL for a playlist.
     *
     * @api
     */
    public function getSourceUrl(Playlist $playlist): string;
}
