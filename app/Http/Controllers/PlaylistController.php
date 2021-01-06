<?php

namespace App\Http\Controllers;

use App\Models\Playlist;

class PlaylistController extends Controller
{
    public function index()
    {
        $playlists = Playlist::orderBy('published_at', 'desc')
            ->withCount('items')
            ->paginate(24);
        return view('playlists.index', [
            'title' => __('Playlists'),
            'playlists' => $playlists,
        ]);
    }

    public function show(Playlist $playlist)
    {
        $playlist->load('channel');
        $items = $playlist->items()
            ->with('video')
            ->orderBy('position', 'asc')
            ->paginate(24);
        return view('playlists.show', [
            'title' => $playlist->title,
            'playlist' => $playlist,
            'items' => $items,
        ]);
    }
}
