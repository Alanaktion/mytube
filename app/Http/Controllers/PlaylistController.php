<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPlaylistImport;
use App\Models\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'sort' => ['sometimes', 'string', 'in:published_at,created_at'],
            'type' => ['sometimes', 'string', 'nullable'],
        ]);
        $sort = $request->input('sort', 'published_at');
        $source = $request->input('source');
        $playlists = Playlist::latest($sort)
            ->with(['firstItem', 'firstItem.video'])
            ->withCount('items');
        if ($source !== null) {
            $playlists->whereHas('channel', function ($query) use ($source) {
                $query->where('type', $source);
            });
        }
        return view('playlists.index', [
            'title' => __('Playlists'),
            'playlists' => $playlists->paginate(24)->withQueryString(),
            'sort' => $sort,
            'source' => $source,
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
        $playlist->load('channel:id,type');
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
