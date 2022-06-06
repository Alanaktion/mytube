<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $type
 * @property int $model_id
 * @property string $model_type
 * @property Channel|Playlist|Video $model
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class JobDetail extends Model
{
    use HasFactory;

    /** @var array<string,string> */
    protected $casts = [
        'data' => 'array',
    ];

    protected $guarded = [];

    /**
     * @return MorphTo<Model,JobDetail>
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
