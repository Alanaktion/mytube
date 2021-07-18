<?php

namespace App\Sources;

use App\Models\Video;

interface SourceVideo
{
    /**
     * Import a video from the source by ID.
     *
     * @api
     */
    public function import(string $id): Video;

    /**
     * Determine if the given URL belongs to this source.
     *
     * @api
     *
     * @return string|null The ID of the video in the URL, or null if unmatched.
     */
    public function matchUrl(string $url): ?string;

    /**
     * Retrieve a canonicalized string for a video ID.
     *
     * This handles preparing an arbitrary ID string for import.
     *
     * @api
     */
    public function canonicalizeId(string $id): string;

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
     * @return string|null The ID of the video in the filename, or null if unmatched.
     */
    public function matchFilename(string $filename): ?string;

    /**
     * Get a source URL for a video.
     *
     * @api
     */
    public function getSourceUrl(Video $video): string;

    /**
     * Get HTML to embed the video, if supported.
     *
     * @api
     */
    public function getEmbedHtml(Video $video): ?string;
}
