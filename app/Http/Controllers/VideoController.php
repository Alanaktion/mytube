<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $videos = Video::with('channel')
            ->latest('published_at');
        $source = $request->input('source');
        if ($source !== null) {
            $videos->where('source_type', $source);
        }
        return view('videos.index', [
            'title' => __('Videos'),
            'videos' => $videos->paginate(24)->withQueryString(),
            'source' => $source,
        ]);
    }

    public function show(Video $video)
    {
        $video->load([
            'channel',
            'playlists' => function ($query) {
                $query->withCount('items');
            },
        ]);
        return view('videos.show', [
            'title' => $video->title,
            'video' => $video,
        ]);
    }
}
