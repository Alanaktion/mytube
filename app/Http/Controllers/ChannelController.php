<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index(Request $request)
    {
        $channels = Channel::latest('published_at')
            ->withCount('videos');
        $source = $request->input('source');
        if ($source !== null) {
            $channels->where('type', $source);
        }
        return view('channels.index', [
            'title' => __('Channels'),
            'channels' => $channels->paginate(36)->withQueryString(),
            'source' => $source,
        ]);
    }

    public function videos(Channel $channel)
    {
        $videos = $channel->videos()
            ->latest('published_at');
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
            ->latest('published_at');
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
            ->latest('published_at');
        $playlists = $channel->playlists()
            ->withCount('items')
            ->latest('published_at');
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
