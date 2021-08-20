<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'sort' => ['sometimes', 'string', 'in:published_at,created_at'],
            'source' => ['sometimes', 'string', 'nullable'],
        ]);
        $sort = $request->input('sort', 'published_at');
        $source = $request->input('source');
        $videos = Video::with('channel')
            ->latest($sort);
        if ($source !== null) {
            $videos->where('source_type', $source);
        }
        return view('videos.index', [
            'title' => __('Videos'),
            'videos' => $videos->paginate(24)->withQueryString(),
            'sort' => $sort,
            'source' => $source,
        ]);
    }

    public function show(Video $video, Request $request)
    {
        if ($request->has('playlist')) {
            $playlist = Playlist::where('uuid', $request->input('playlist'))->first();
            if ($playlist) {
                return $this->showWithPlaylist($video, $playlist);
            }
        }
        $video->load([
            'channel',
            'playlists' => function ($query): void {
                $query
                    ->with(['firstItem', 'firstItem.video'])
                    ->withCount('items');
            },
        ]);
        return view('videos.show', [
            'title' => $video->title,
            'video' => $video,
            'playlist' => null,
        ]);
    }

    public function showWithPlaylist(Video $video, Playlist $playlist)
    {
        $playlist->load([
            'items' => function ($query): void {
                $query->orderBy('position', 'asc');
            },
            'items.video:id,uuid,title,channel_id',
            'items.video.channel:id,title',
        ]);
        $video->load('channel');
        return view('videos.show', [
            'title' => $video->title,
            'video' => $video,
            'playlist' => $playlist,
        ]);
    }
}
