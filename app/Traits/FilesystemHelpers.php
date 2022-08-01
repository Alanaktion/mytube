<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

trait FilesystemHelpers
{
    /**
     * Get all of the files in a directory recursively.
     *
     * This returns an array of all files in a directory, with complete paths.
     * @return string[]
     */
    protected function getDirectoryFiles(string $directory): array
    {
        $files = [];
        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $directory,
                RecursiveDirectoryIterator::FOLLOW_SYMLINKS
            ),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        foreach ($rii as $file) {
            /** @var \SplFileInfo $file */
            if ($file->isDir()) {
                continue;
            }
            $path = $file->getRealPath();
            if ($path !== false) {
                $files[] = $path;
            }
        }
        return $files;
    }

    /**
     * Copy a thumbnail image file to the storage directory.
     */
    protected function copyThumbnailImage(string $imagePath): string
    {
        $disk = Storage::disk('public');
        $destPath = 'thumbs/local/' . basename($imagePath);
        $data = file_get_contents($imagePath);
        $disk->put($destPath, $data, 'public');
        return Storage::url("public/$destPath");
    }
}
