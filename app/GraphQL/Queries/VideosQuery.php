<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\GraphQL\Middleware\ResolvePage;
use App\Models\Video;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class VideosQuery extends Query
{
    /** @var class-string[] */
    protected $middleware = [
        ResolvePage::class,
    ];

    /** @var array<string, string> */
    protected $attributes = [
        'name' => 'videos',
        'description' => 'A list of Video resources',
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Video');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function args(): array
    {
        return [
            // Field filters
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
            ],
            'uuid' => [
                'name' => 'uuid',
                'type' => Type::string(),
            ],
            'channel_id' => [
                'name' => 'channel_id',
                'type' => Type::int(),
            ],
            'search' => [
                'name' => 'search',
                'type' => Type::string(),
            ],

            // Pagination
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
            ],
        ];
    }

    /**
     * @param object $root
     * @param array<string, mixed> $args
     * @param mixed $context
     * @return mixed
     */
    public function resolve($root, $args, $context, SelectFields $fields)
    {
        $videos = Video::with($fields->getRelations())
            ->select(array_merge($fields->getSelect(), ['uuid', 'source_type']))
            ->latest('id');

        if (isset($args['id'])) {
            $videos->where('id', $args['id']);
        }
        if (isset($args['uuid'])) {
            $videos->where('uuid', $args['uuid']);
        }
        if (isset($args['channel_id'])) {
            $videos->where('channel_id', $args['channel_id']);
        }
        if (isset($args['search'])) {
            $q = strtr($args['search'], ' ', '%');
            $videos
                ->where('title', 'like', "%$q%")
                ->orWhere('uuid', $q);
        }

        return $videos->paginate(15, ['*'], 'page');
    }
}
