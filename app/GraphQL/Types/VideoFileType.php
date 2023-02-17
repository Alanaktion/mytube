<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\VideoFile;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class VideoFileType extends GraphQLType
{
    /** @var array<string, string> */
    protected $attributes = [
        'name' => 'VideoFile',
        'description' => 'A file for a video',
        'model' => VideoFile::class,
    ];

    /**
     * @return array<string, array<string, mixed>>
     */
    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The instance-specific ID for the file.',
            ],
            'url' => [
                'type' => Type::string(),
                'description' => 'The URL to the video file.',
                'alias' => 'path',
                // 'selectable' => false,
                'resolve' => fn (VideoFile $root): ?string => $root->url,
            ],
            'mime_type' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The mime-type for the video file.',
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'An ISO 8601 date of when the video file was added.',
            ],
        ];
    }
}
