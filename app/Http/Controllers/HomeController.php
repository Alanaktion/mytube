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

    public function videos()
    {
        $videos = Video::with('channel')
            ->orderBy('published_at', 'desc')
            ->paginate(24);
        return view('videos', [
            'title' => __('Videos'),
            'videos' => $videos,
        ]);
    }

    public function videoShow(Video $video)
    {
        $video->load([
            'channel',
            'playlists' => function ($query) {
                $query->withCount('items');
            },
        ]);
        return view('videoShow', [
            'title' => $video->title,
            'video' => $video,
        ]);
    }

    public function playlists()
    {
        $playlists = Playlist::orderBy('published_at', 'desc')
            ->withCount('items')
            ->paginate(24);
        return view('playlists', [
            'title' => __('Playlists'),
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
            'title' => $playlist->title,
            'playlist' => $playlist,
            'items' => $items,
        ]);
    }
}
