<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPlaylistImport;
use App\Models\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Playlist::class);
    }

    public function index(Request $request)
    {
        $request->validate([
            'sort' => ['sometimes', 'string', 'in:published_at,created_at'],
            'type' => ['sometimes', 'string', 'nullable'],
        ]);
        $sort = $request->input('sort', 'published_at');
        $source = $request->input('source');
        $playlists = Playlist::latest($sort)
            ->latest('id')
            ->with(['firstItem', 'firstItem.video'])
            ->withCount('items')
            ->withDuration();
        if ($source !== null) {
            $playlists->whereHas('channel', function ($query) use ($source): void {
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
            ->with([
                'video' => function ($query): void {
                    $query->withCount('files')
                        ->withMax('files', 'height');
                },
                'video.channel',
            ])
            ->orderBy('position', 'asc')
            ->paginate(24);
        return view('playlists.show', [
            'title' => $playlist->title,
            'playlist' => $playlist,
            'items' => $items,
        ]);
    }

    public function update(Playlist $playlist)
    {
        $playlist->load('channel:id,type');
        ProcessPlaylistImport::dispatch($playlist->channel->type, $playlist->uuid);
        $message = 'Playlist refresh queued.';
        if (config('queue.default') == 'sync') {
            $message = 'Playlist refreshed.';
        }
        return redirect()
            ->route('playlists.show', ['playlist' => $playlist])
            ->with('message', $message);
    }

    public function destroy(Playlist $playlist, Request $request)
    {
        $request->validate([
            'videos' => ['sometimes', 'boolean'],
            'files' => ['sometimes', 'boolean'],
        ]);
        $playlist->delete($request->boolean('videos'), $request->boolean('files'));
        return redirect()
            ->route('playlists.index')
            ->with('message', 'Playlist deleted.');
    }
}
