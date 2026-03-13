<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

trait FilesystemHelpers
{
    /**
     * Find a sidecar image file for a video path without using glob patterns.
     *
     * @param string[] $allowedExtensions
     */
    protected function findSidecarImageFile(
        string $videoFile,
        array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp']
    ): ?string {
        $allowed = array_values(array_unique(array_map(
            static fn (string $ext): string => strtolower(ltrim($ext, '.')),
            $allowedExtensions
        )));

        $basePath = preg_replace('/\.[^.]+$/', '', $videoFile);
        if (is_string($basePath) && $basePath !== '') {
            foreach ($allowed as $ext) {
                $candidate = $basePath . '.' . $ext;
                if (is_file($candidate)) {
                    return $candidate;
                }
            }
        }

        $videoBaseName = pathinfo($videoFile, PATHINFO_FILENAME);
        $directory = dirname($videoFile);
        $entries = @scandir($directory);
        if ($entries === false) {
            return null;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $fullPath = $directory . DIRECTORY_SEPARATOR . $entry;
            if (!is_file($fullPath)) {
                continue;
            }

            $ext = strtolower((string) pathinfo($entry, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) {
                continue;
            }

            if (str_starts_with(pathinfo($entry, PATHINFO_FILENAME), $videoBaseName)) {
                return $fullPath;
            }
        }

        return null;
    }

    /**
     * Find a sidecar info.json file for a video path without using glob patterns.
     */
    protected function findSidecarInfoJsonFile(string $videoFile, ?string $id = null): ?string
    {
        $candidates = [
            $videoFile . '.info.json',
            (string) preg_replace('/\.[^.]+$/', '', $videoFile) . '.info.json',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        $directory = dirname($videoFile);
        $videoBaseName = pathinfo($videoFile, PATHINFO_FILENAME);
        $entries = @scandir($directory);
        if ($entries === false) {
            return null;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            if (!str_ends_with($entry, '.info.json')) {
                continue;
            }

            $fullPath = $directory . DIRECTORY_SEPARATOR . $entry;
            if (!is_file($fullPath)) {
                continue;
            }

            if (str_starts_with($entry, $videoBaseName . '.')) {
                return $fullPath;
            }

            if ($id !== null && $id !== '' && str_contains($entry, $id)) {
                return $fullPath;
            }
        }

        return null;
    }

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
        $destPath = 'thumbs/local/' . Str::slug(basename($imagePath));
        $data = file_get_contents($imagePath);
        $disk->put($destPath, $data, 'public');
        return Storage::url("public/$destPath");
    }
}
