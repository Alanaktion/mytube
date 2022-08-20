<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use LogicException;

/**
 * @property int $id
 * @property int $video_id
 * @property string $path
 * @property string $mime_type
 * @property int|null $size
 * @property int|null $width
 * @property int|null $height
 * @property string|null $duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $url
 * @property-read Video|null $video
 */
class VideoFile extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $guarded = [];

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
    public function url(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                if (!$this->path || !$this->video) {
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

    /**
     * Get metadata for the specified video file, if ffprobe is available.
     * @return array<string,int|string>|null
     */
    public static function getMetadataForFile(string $filePath): ?array
    {
        try {
            $raw = shell_exec('ffprobe ' . implode(' ', [
                '-v error',
                '-select_streams v',
                '-show_entries stream=width,height',
                '-show_entries format=duration',
                '-of json',
                escapeshellarg($filePath),
            ]));
            if (!$raw) {
                return null;
            }
            $output = json_decode($raw);
            if (!$output) {
                return null;
            }

            return [
                'width' => $output->streams[0]->width,
                'height' => $output->streams[0]->height,
                'duration' => (int)$output->format->duration,
            ];
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Delete the model from the database.
     *
     * @param bool $filesystem Delete the file from the filesystem.
     */
    public function delete(bool $filesystem = true)
    {
        if ($filesystem) {
            @unlink($this->path);
        }
        parent::delete();
    }
}
