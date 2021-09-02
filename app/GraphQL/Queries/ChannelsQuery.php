<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\GraphQL\Middleware\ResolvePage;
use App\Models\Channel;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class ChannelsQuery extends Query
{
    /** @var class-string[] */
    protected $middleware = [
        ResolvePage::class,
    ];

    /** @var array<string, string> */
    protected $attributes = [
        'name' => 'channels',
        'description' => 'A list of Channel resources',
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Channel');
    }

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
        $channels = Channel::with($fields->getRelations())
            ->select(array_merge($fields->getSelect(), ['uuid']))
            ->latest('id');

        if (isset($args['id'])) {
            $channels->where('id', $args['id']);
        }
        if (isset($args['uuid'])) {
            $channels->where('uuid', $args['uuid']);
        }
        if (isset($args['search'])) {
            $q = strtr($args['search'], ' ', '%');
            $channels
                ->where('title', 'like', "%$q%")
                ->orWhere('uuid', $q);
        }

        return $channels->paginate(15, ['*'], 'page');
    }
}
