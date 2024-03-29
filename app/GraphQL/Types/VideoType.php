<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Video;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class VideoType extends GraphQLType
{
    /** @var array<string, string> */
    protected $attributes = [
        'name' => 'Video',
        'description' => 'A video from a channel',
        'model' => Video::class,
    ];

    /**
     * @return array<string, array<string, mixed>>
     */
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
            'source_link' => [
                'type' => Type::string(),
                'description' => 'The URL to the original source video.',
                'selectable' => false,
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
            'files' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type('VideoFile'))),
                'description' => 'The video files available.',
                'query' => function (array $args, $query, $ctx): void {
                    // TODO: remove this when video UUID is no longer required for file symlink
                    $query->with('video:id,uuid');
                },
            ],
        ];
    }
}
