<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Video;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $videos = Video::latest()
            ->limit(18)
            ->get();
        $channels = Channel::latest()
            ->limit(6)
            ->get();
        return view('home', [
            'videos' => $videos,
            'channels' => $channels,
        ]);
    }

    public function videos()
    {
        return view('videos', [
            'videos' => Video::orderBy('published_at', 'desc')->get(),
        ]);
    }

    public function channels()
    {
        return view('channels', [
            'channels' => Channel::orderBy('published_at', 'desc')->get(),
        ]);
    }
}
