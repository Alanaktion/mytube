<?php

namespace App\Http\Controllers;

use App\Models\Channel;

class ChannelVideoController extends Controller
{
    public function index(Channel $channel)
    {
        $videos = $channel->videos()
            ->withCount('files')
            ->latest('published_at');
        return view('channels.videos', [
            'title' => $channel->title,
            'channel' => $channel,
            'videos' => $videos->paginate(24),
        ]);
    }
}
