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
        // TODO: handle explicitly matching exact UUIDs, and prioritizing more recent objects
        $videos = Video::search($request->input('q'))->paginate(24);
        $playlists = Playlist::search($request->input('q'))->paginate(18);
        $channels = Channel::search($request->input('q'))->paginate(18);

        return view('search', [
            'videos' => $videos,
            'playlists' => $playlists,
            'channels' => $channels,
            'q' => $request->input('q'),
        ]);
    }
}
