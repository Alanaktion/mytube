<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Video;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class VideoType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Video',
        'description' => 'A video from a channel',
        'model' => Video::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The instance-specific ID for the video.',
            ],
            'uuid' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The unique ID for the video, from its source.',
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The title of the video.',
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of the video, as defined by the original uploader.',
            ],
            'thumbnail_url' => [
                'type' => Type::string(),
                'description' => 'A relative URL for the video\'s thumbnail image.',
            ],
            'poster_url' => [
                'type' => Type::string(),
                'description' => 'A relative URL for the video\'s poster image.',
            ],
            'published_at' => [
                'type' => Type::string(),
                'description' => 'An ISO 8601 date of when the video was initially published.',
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'An ISO 8601 date of when the video was archived.',
            ],
            'channel' => [
                'type' => GraphQL::type('Channel'),
                'description' => 'The channel where the video was published.',
            ],
        ];
    }
}
