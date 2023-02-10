<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;

class CheckForMaintenanceMode extends Middleware
{
    /**
     * The URIs that should be accessible while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
