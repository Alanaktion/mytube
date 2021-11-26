<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VideoFile extends Model
{
    use HasFactory;

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Get a web-accessible path/URL to the source file.
     *
     * This currently creates a symlink to the source file in the public disk.
     *
     * @todo support remote file storage, e.g. storing video files on B2/S3.
     */
    public function getUrlAttribute(): ?string
    {
        if (!$this->path) {
            return null;
        }

        // Symlinks on Windows are just a pain and I don't care.
        if (DIRECTORY_SEPARATOR === '\\') {
            return null;
        }

        $ext = pathinfo($this->path, PATHINFO_EXTENSION);
        $file = "{$this->uuid}.{$ext}";

        // Ensure video directory exists
        Storage::makeDirectory('public/videos');

        // Create symlink if it doesn't exist
        $linkPath = storage_path("app/public/videos") . DIRECTORY_SEPARATOR . $file;
        if (!is_link($linkPath) && is_file($this->path)) {
            symlink($this->path, $linkPath);
        }

        return url("/storage/videos/$file");
    }
}
