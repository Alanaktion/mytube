<?php

namespace App\Sources;

use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Video;

interface Source
{
    /**
     * Get the source type identifier.
     *
     * This is the unique string that is added to the "source_type" field on
     * stored videos and channels.
     *
     * @api
     */
    public function getSourceType(): string;

    /**
     * Get the app field that is used to import a channel.
     *
     * This must be either "uuid" or "custom_url".
     *
     * @api
     */
    public function getChannelField(): string;

    /**
     * Retrieve a canonicalized string for a video ID.
     *
     * This handles preparing an arbitrary ID string for import.
     *
     * @api
     */
    public function canonicalizeVideo(string $id): string;

    /**
     * Import a channel from the source by ID.
     *
     * @api
     */
    public function importChannel(string $id): Channel;

    /**
     * Import a video from the source by ID.
     *
     * @api
     */
    public function importVideo(string $id): Video;

    /**
     * Determine if this source supports playlists.
     *
     * @api
     */
    public function supportsPlaylists(): bool;

    /**
     * Import a playlist from the source by ID, if supported by the source.
     *
     * This method should throw an exception if not supported.
     *
     * @api
     */
    public function importPlaylist(string $id): Playlist;

    /**
     * Import items from a playlist, if supported by the source.
     *
     * This method should throw an exception if not supported.
     *
     * @api
     */
    public function importPlaylistItems(Playlist $playlist): void;

    /**
     * Determine if the given URL belongs to this source.
     *
     * @api
     *
     * @return string|null The ID of the video in the URL, or null if unmatched.
     */
    public function matchUrl(string $url): ?string;

    /**
     * Determine if the given ID belongs to this source.
     *
     * @api
     */
    public function matchId(string $id): bool;

    /**
     * Determine if the given filename belongs to this source.
     *
     * @api
     *
     * @return string|null  The ID of the video in the filename, or null if unmatched.
     */
    public function matchFilename(string $filename): ?string;
}
