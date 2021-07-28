<?php

namespace App\Sources;

use Illuminate\View\View;

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
     * Get the display name for the source type.
     *
     * @api
     */
    public function getDisplayName(): string;

    /**
     * Get the Blade template for the source type icon.
     *
     * This is typically an SVG document, and should include a way to merge
     * classes into the root element.
     *
     * @api
     */
    public function getIcon(): View;

    /**
     * The the source class for managing videos
     *
     * @api
     */
    public function video(): SourceVideo;

    /**
     * The the source class for managing channels
     *
     * @api
     */
    public function channel(): SourceChannel;

    /**
     * The the source class for managing playlists, if supported
     *
     * @api
     */
    public function playlist(): ?SourcePlaylist;
}
