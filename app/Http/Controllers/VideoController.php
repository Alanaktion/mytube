<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sort' => ['sometimes', 'string', 'in:published_at,created_at'],
            'source' => ['sometimes', 'string', 'nullable'],
            'files' => ['sometimes', 'boolean', 'nullable'],
        ]);
        if ($validator->fails()) {
            dd($validator->errors());
        }
        $sort = $request->input('sort', 'published_at');
        $source = $request->input('source');
        $videos = Video::with('channel')
            ->withCount('files')
            ->latest($sort);
        if ($source !== null) {
            $videos->where('source_type', $source);
        }
        if ($request->has('files')) {
            if ($request->boolean('files')) {
                $videos->whereHas('files');
            } else {
                $videos->whereDoesntHave('files');
            }
        }
        return view('videos.index', [
            'title' => __('Videos'),
            'videos' => $videos->paginate(24)->withQueryString(),
            'sort' => $sort,
            'source' => $source,
            'errors' => $validator->errors(),
        ]);
    }

    public function show(Video $video, Request $request)
    {
        $playlist = null;
        if ($request->has('playlist')) {
            $playlist = Playlist::where('uuid', $request->input('playlist'))
                ->whereHas('items', function (Builder $query) use ($video) {
                    $query->where('video_id', $video->id);
                })
                ->with([
                    'items' => function ($query): void {
                        $query->select(['id', 'playlist_id', 'video_id'])
                            ->orderBy('position', 'asc');
                    },
                    'items.video:id,uuid,title,channel_id,thumbnail_url',
                    'items.video.channel:id,title',
                ])
                ->first();
        }

        $video->load([
            'channel',
            'files',
            'files.video:id,uuid',
        ]);
        if ($playlist === null) {
            $video->load([
                'playlists' => function ($query): void {
                    $query
                        ->with(['firstItem', 'firstItem.video'])
                        ->withCount('items');
                },
            ]);
        }

        // File collection with specific info required for download component
        $files = $video->files->map(fn($v) => [
            'id' => $v->id,
            'url' => $v->url,
            'size' => $v->size,
            'height' => $v->height,
            'mime_type' => $v->mime_type,
        ]);
        return view('videos.show', [
            'title' => $video->title,
            'video' => $video,
            'files' => $files,
            'playlist' => $playlist,
        ]);
    }
}
