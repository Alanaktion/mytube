<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class UserQuery extends Query
{
    protected $attributes = [
        'name' => 'user',
        'description' => 'The currently authenticated user.',
    ];

    public function type(): Type
    {
        return GraphQL::type('User');
    }

    public function resolve($root, $args, $context, SelectFields $fields)
    {
        return Auth::user();
    }
}
