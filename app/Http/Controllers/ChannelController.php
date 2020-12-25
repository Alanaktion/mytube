<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index()
    {
        return view('channels.index', [
            'channels' => Channel::orderBy('published_at', 'desc')
                ->withCount('videos')
                ->get(),
        ]);
    }

    public function videos(Channel $channel)
    {
        $videos = $channel->videos()
            ->orderBy('published_at', 'desc');
        return view('channels.videos', [
            'title' => $channel->title,
            'channel' => $channel,
            'videos' => $videos->paginate(24),
        ]);
    }

    public function playlists(Channel $channel)
    {
        $playlists = $channel->playlists()
            ->withCount('items')
            ->orderBy('published_at', 'desc');
        return view('channels.playlists', [
            'title' => $channel->title,
            'channel' => $channel,
            'playlists' => $playlists->get(),
        ]);
    }

    public function search(Channel $channel, Request $request)
    {
        // TODO: show video and playlists results in single list
        $videos = $channel->videos()
            ->orderBy('published_at', 'desc');
        $playlists = $channel->playlists()
            ->withCount('items')
            ->orderBy('published_at', 'desc');
        $q = strtr($request->input('q'), ' ', '%');
        if ($q) {
            $videos->where('title', 'like', "%$q%");
            $playlists->where('title', 'like', "%$q%");
        }
        return view('channels.search', [
            'title' => $channel->title,
            'channel' => $channel,
            'videos' => $videos->paginate(24),
            'playlists' => $playlists->get(),
            'channelQ' => $request->input('q'),
        ]);
    }

    public function about(Channel $channel)
    {
        return view('channels.about', [
            'title' => $channel->title,
            'channel' => $channel,
        ]);
    }
}
