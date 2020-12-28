<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Channel;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ChannelType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Channel',
        'description' => 'A channel on a third-party site that videos are uploaded to',
        'model' => Channel::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The instance-specific ID for the channel.',
            ],
            'uuid' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The unique ID for the channel, from its source.',
            ],
            'type' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The source type of the channel.',
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The name of the channel.',
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of the channel, as defined by the original uploader.',
            ],
            'image_url' => [
                'type' => Type::string(),
                'description' => 'A relative URL for the channel\'s profile image.',
            ],
            'image_url_lg' => [
                'type' => Type::string(),
                'description' => 'A relative URL for the channel\'s large profile image.',
            ],
            'source_link' => [
                'type' => Type::string(),
                'description' => 'The URL to the original source channel.',
                'selectable' => false,
            ],
            'published_at' => [
                'type' => Type::string(),
                'description' => 'An ISO 8601 date of when the channel was created.',
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'An ISO 8601 date of when the channel was first archived.',
            ],
        ];
    }
}
