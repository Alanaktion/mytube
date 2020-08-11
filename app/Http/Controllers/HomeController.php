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
        $videos = Video::orderBy('published_at', 'desc')
            ->simplePaginate(24);
        return view('videos', [
            'videos' => $videos,
        ]);
    }

    public function channels()
    {
        return view('channels', [
            'channels' => Channel::orderBy('published_at', 'desc')->get(),
        ]);
    }

    public function videoShow(Video $video)
    {
        $video->load('channel');
        return view('videoShow', [
            'video' => $video,
        ]);
    }

    public function channelShow(Channel $channel)
    {
        $videos = $channel->videos()
            ->orderBy('published_at', 'desc')
            ->simplePaginate(24);
        return view('channelShow', [
            'channel' => $channel,
            'videos' => $videos,
        ]);
    }
}
