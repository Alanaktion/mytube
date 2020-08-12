<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $guarded = [];
    protected $dates = ['published_at'];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Get a web-accessible symlink to the source file
     */
    public function link(): string
    {
        $ext = pathinfo($this->file_path, PATHINFO_EXTENSION);
        $file = "{$this->uuid}.{$ext}";

        // Ensure video directory exists
        if (!is_dir(storage_path('app/public/videos'))) {
            mkdir(storage_path('app/public/videos'), 0755, true);
        }

        // Create symlink if it doesn't exist
        $linkPath = storage_path('app/public/videos') . DIRECTORY_SEPARATOR . $file;
        if (!is_link($linkPath) && is_file($this->file_path)) {
            symlink($this->file_path, $linkPath);
        }

        return "/storage/videos/$file";
    }
}
