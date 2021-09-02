<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $uuid
 * @property string|null $file_path
 * @property string $type
 * @property string $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ImportError extends Model
{
    /**
     * @var string[]
     */
    protected $guarded = [];
}
