<?php

namespace App\Http\Controllers;

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

    public function show(Video $video)
    {
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
        ]);
    }
}
