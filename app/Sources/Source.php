<?php

namespace App\Sources;

use App\Models\Channel;
use App\Models\Video;

interface Source
{
    /**
     * Get the source type identifier.
     *
     * This is the unique string that is added to the "source_type" field on
     * stored videos and channels.
     */
    public function getSourceType(): string;

    /**
     * Get the app field that is used to import a channel.
     *
     * This must be either "uuid" or "custom_url".
     */
    public function getChannelField(): string;

    /**
     * Retrieve a canonicalized string for a video ID.
     *
     * This handles preparing an arbitrary ID string for import.
     */
    public function canonicalizeVideo(string $id): string;

    /**
     * Import a channel from the source by ID.
     */
    public function importChannel(string $id): Channel;

    /**
     * Import a video from the source by ID.
     */
    public function importVideo(string $id): Video;

    /**
     * Determine if the given URL belongs to this source.
     *
     * @return string|null The ID of the video in the URL, or null if unmatched.
     */
    public function matchUrl(string $url): ?string;

    /**
     * Determine if the given ID belongs to this source.
     */
    public function matchId(string $id): bool;

    /**
     * Determine if the given filename belongs to this source.
     *
     * @return string|null  The ID of the video in the filename, or null if unmatched.
     */
    public function matchFilename(string $filename): ?string;
}
