<?php

declare(strict_types=1);

namespace App\GraphQL\Middleware;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Pagination\Paginator;
use Rebing\GraphQL\Support\Middleware;

class ResolvePage extends Middleware
{
    /**
     * @param array<string, mixed> $args
     */
    public function handle(mixed $root, array $args, mixed $context, ResolveInfo $info, Closure $next): mixed
    {
        Paginator::currentPageResolver(fn () => $args['page'] ?? 1);

        return $next($root, $args, $context, $info);
    }
}
