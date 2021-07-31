<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'User',
        'description' => 'A registered user',
        'model' => User::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The user\'s ID.',
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The user\'s name.',
            ],
            'email' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The user\'s email address.',
            ],
        ];
    }
}
