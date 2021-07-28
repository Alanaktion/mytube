<?php

namespace App\Sources;

use App\Models\Channel;

interface SourceChannel
{
    /**
     * Get the app field that is used to import a channel.
     *
     * This must be either "uuid" or "custom_url".
     *
     * @api
     */
    public function getField(): string;

    /**
     * Import a channel from the source by ID.
     *
     * @api
     */
    public function import(string $id): Channel;

    /**
     * Determine if the given URL belongs to this source.
     *
     * @api
     *
     * @return string|null The app field of the channel in the URL, or null if unmatched.
     */
    public function matchUrl(string $url): ?string;

    /**
     * Get a source URL for a channel.
     *
     * @api
     */
    public function getSourceUrl(Channel $channel): string;
}
