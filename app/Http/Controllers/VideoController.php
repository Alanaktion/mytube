<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Video::class);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sort' => ['sometimes', 'string', 'in:published_at,created_at'],
            'source' => ['sometimes', 'string', 'nullable'],
            'visibility' => ['sometimes', 'string', 'nullable', 'in:public,unlisted,private'],
            'files' => ['sometimes', 'boolean', 'nullable'],
            'resolution' => ['sometimes', 'string', 'nullable'],
        ]);
        if ($validator->fails()) {
            return redirect()->route('videos.index')->with([
                'message' => implode("\n", $validator->errors()->all()),
                'messageType' => 'error',
            ]);
        }
        $sort = $request->input('sort', 'published_at');
        $source = $request->input('source');
        $videos = Video::with('channel')
            ->withCount('files')
            ->withMax('files', 'height')
            ->latest($sort)
            ->latest('id');
        if ($source !== null) {
            $videos->where('source_type', $source);
        }
        if ($request->input('visibility')) {
            $videos->where('source_visibility', $request->input('visibility'));
        }
        if ($request->input('resolution')) {
            $videos->whereHas('files', function (Builder $query) use ($request) {
                if ($request->input('resolution') == 'portrait') {
                    $query->where('height', '<', 'width');
                } else {
                    $query->where('height', $request->input('resolution'));
                }
            });
        }
        if ($request->input('mime_type')) {
            $videos->whereHas('files', function (Builder $query) use ($request) {
                $query->where('mime_type', $request->input('mime_type'));
            });
        }
        if ($request->filled('files') && !$request->input('resolution') && !$request->input('mime_type')) {
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

    public function destroy(Video $video, Request $request)
    {
        $request->validate([
            'files' => ['sometimes', 'boolean'],
        ]);
        $video->delete($request->boolean('files'));
        return redirect()
            ->route('videos.index')
            ->with('message', 'Video deleted.');
    }
}
