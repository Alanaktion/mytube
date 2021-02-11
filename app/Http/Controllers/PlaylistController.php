<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPlaylistImport;
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

    public function refresh(Playlist $playlist)
    {
        if ($playlist->channel->type != 'youtube') {
            return abort(400);
        }
        ProcessPlaylistImport::dispatch($playlist->uuid);
        $message = 'Playlist refresh queued.';
        if (config('queue.default') == 'sync') {
            $message = 'Playlist refreshed.';
        }
        return redirect()
            ->route('playlist', ['playlist' => $playlist])
            ->with('message', $message);
    }
}
