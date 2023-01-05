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
     * @param object $root
     * @param array<string, mixed> $args
     * @param mixed $context
     * @return mixed
     */
    public function handle($root, $args, $context, ResolveInfo $info, Closure $next)
    {
        Paginator::currentPageResolver(fn() => $args['page'] ?? 1);

        return $next($root, $args, $context, $info);
    }
}
