<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait DownloadsImages
{
    /**
     * Download an image file from a URL, returning the new local URL.
     *
     * If the file already exists at the destination, it will not be downloaded again.
     *
     * @param string $path The destination, either a full path or a directory.
     * @return string|null The new URL, or null if the file could not be downloaded.
     */
    public function downloadImage(string $url, string $path): ?string
    {
        if (!str_contains($path, '.')) {
            $urlPath = parse_url($url, PHP_URL_PATH);
            $path = Str::finish($path, '/') . basename($urlPath);
        }
        $disk = Storage::disk('public');
        if (!$disk->exists($path)) {
            try {
                $image = file_get_contents($url);
                $disk->put($path, $image, 'public');
            } catch (\Exception) {
                return null;
            }
        }
        return Storage::url("public/$path");
    }
}
