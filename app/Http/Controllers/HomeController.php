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
        $videos = Video::latest('id')
            ->with('channel')
            ->limit(18)
            ->get();
        $playlists = Playlist::latest('id')
            ->withCount('items')
            ->limit(6)
            ->get();
        $channels = Channel::latest('id')
            ->withCount('videos')
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
        $q = strtr($request->input('q'), ' ', '%');

        $videos = Video::latest('id')
            ->with('channel')
            ->where('title', 'like', "%$q%")
            ->orWhere('uuid', $q)
            ->limit(24)
            ->get();
        $playlists = Playlist::latest('id')
            ->withCount('items')
            ->where('title', 'like', "%$q%")
            ->orWhere('uuid', $q)
            ->limit(18)
            ->get();
        $channels = Channel::latest('id')
            ->withCount('videos')
            ->where('title', 'like', "%$q%")
            ->orWhere('uuid', $q)
            ->limit(18)
            ->get();

        return view('search', [
            'videos' => $videos,
            'playlists' => $playlists,
            'channels' => $channels,
            'q' => $request->input('q'),
        ]);
    }
}
