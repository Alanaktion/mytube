<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;

class AdminQueueController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            if (config('queue.default') != 'redis') {
                return abort(503);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $range = Redis::zrange('queues:default:delayed', 0, -1);
        return view('admin.queues.index', [
            'range' => $range,
        ]);
    }
}
