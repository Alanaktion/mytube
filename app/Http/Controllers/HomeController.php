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
            ->limit(18)
            ->get();
        $playlists = Playlist::latest()
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
            ->where('title', 'like', "%$q%")
            ->orWhere('uuid', $q)
            ->limit(24)
            ->get();
        $channels = Channel::latest()
            ->where('title', 'like', "%$q%")
            ->orWhere('uuid', $q)
            ->limit(18)
            ->get();

        return view('search', [
            'videos' => $videos,
            'channels' => $channels,
            'q' => $q,
        ]);
    }

    public function videos()
    {
        $videos = Video::orderBy('published_at', 'desc')
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

    public function channelShow(Channel $channel)
    {
        $videos = $channel->videos()
            ->orderBy('published_at', 'desc')
            ->paginate(24);
        return view('channelShow', [
            'channel' => $channel,
            'videos' => $videos,
        ]);
    }

    public function playlists()
    {
        $playlists = Playlist::orderBy('published_at', 'desc')
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
