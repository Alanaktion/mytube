<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $videos = Video::latest()
            ->with('channel')
            ->limit(18)
            ->get();
        $playlists = Playlist::latest()
            ->withCount('items')
            ->limit(6)
            ->get();
        $channels = Channel::latest()
            ->limit(6)
            ->get();
        return view('home', [
            'videos' => $videos,
            'playlists' => $playlists,
            'channels' => $channels,
        ]);
    }

    public function search(Request $request)
    {
        $q = $request->input('q');

        $videos = Video::latest()
            ->with('channel')
            ->where('title', 'like', "%$q%")
            ->orWhere('uuid', $q)
            ->limit(24)
            ->get();
        $playlists = Playlist::latest()
            ->withCount('items')
            ->where('title', 'like', "%$q%")
            ->orWhere('uuid', $q)
            ->limit(18)
            ->get();
        $channels = Channel::latest()
            ->where('title', 'like', "%$q%")
            ->orWhere('uuid', $q)
            ->limit(18)
            ->get();

        return view('search', [
            'videos' => $videos,
            'playlists' => $playlists,
            'channels' => $channels,
            'q' => $q,
        ]);
    }

    public function videos()
    {
        $videos = Video::with('channel')
            ->orderBy('published_at', 'desc')
            ->paginate(24);
        return view('videos', [
            'videos' => $videos,
        ]);
    }

    public function videoShow(Video $video)
    {
        $video->load('channel');
        return view('videoShow', [
            'video' => $video,
        ]);
    }

    public function channels()
    {
        return view('channels', [
            'channels' => Channel::orderBy('published_at', 'desc')->get(),
        ]);
    }

    public function channelShow(Channel $channel, Request $request)
    {
        $videos = $channel->videos()
            ->orderBy('published_at', 'desc');
        $playlists = $channel->playlists()
            ->withCount('items')
            ->orderBy('published_at', 'desc');
        $q = $request->input('q');
        if ($q) {
            $videos->where('title', 'like', "%$q%");
            $playlists->where('title', 'like', "%$q%");
        }
        return view('channelShow', [
            'channel' => $channel,
            'videos' => $videos->paginate(24),
            'playlists' => $playlists->get(),
            'channelQ' => $q,
        ]);
    }

    public function playlists()
    {
        $playlists = Playlist::orderBy('published_at', 'desc')
            ->withCount('items')
            ->paginate(24);
        return view('playlists', [
            'playlists' => $playlists,
        ]);
    }

    public function playlistShow(Playlist $playlist)
    {
        $playlist->load('channel');
        $items = $playlist->items()
            ->with('video')
            ->orderBy('position', 'asc')
            ->paginate(24);
        return view('playlistShow', [
            'playlist' => $playlist,
            'items' => $items,
        ]);
    }
}
