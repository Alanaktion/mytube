<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MetaController extends Controller
{
    /**
     * @link https://www.robotstxt.org/
     */
    public function robots()
    {
        $disallow = '';
        if (!config('app.robots')) {
            $disallow = ' /';
        }
        $text = 'User-agent: *' . PHP_EOL . 'Disallow:' . $disallow . PHP_EOL;
        return response($text, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * @link https://developer.mozilla.org/en-US/docs/Web/OpenSearch
     */
    public function openSearch()
    {
        return response(view('meta.opensearch'))
            ->header('Content-Type', 'application/xml');
    }
}
