<?php

namespace App\Http\Controllers;

use App\Models\Video;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::with('channel')
            ->orderBy('published_at', 'desc')
            ->paginate(24);
        return view('videos.index', [
            'title' => __('Videos'),
            'videos' => $videos,
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
