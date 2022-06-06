<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $video_id
 * @property string $path
 * @property string $mime_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Video $video
 */
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
     * @todo remove need for accessing Video relation for UUID.
     *
     * @return Attribute<?string,void>
     */
    public function getUrl(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                if (!$this->path) {
                    return null;
                }

                // Symlinks on Windows are just a pain and I don't care.
                if (DIRECTORY_SEPARATOR === '\\') {
                    return null;
                }

                $ext = pathinfo($this->path, PATHINFO_EXTENSION);
                $file = "{$this->video->uuid}.{$ext}";

                // Ensure video directory exists
                Storage::makeDirectory('public/videos');

                // Create symlink if it doesn't exist
                $linkPath = storage_path("app/public/videos") . DIRECTORY_SEPARATOR . $file;
                if (!is_link($linkPath) && is_file($this->path)) {
                    symlink($this->path, $linkPath);
                }

                return url("/storage/videos/$file");
            }
        );
    }
}
