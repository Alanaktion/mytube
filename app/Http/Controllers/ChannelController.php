<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'sort' => ['sometimes', 'string', 'in:published_at,created_at'],
            'source' => ['sometimes', 'string', 'nullable'],
        ]);
        $sort = $request->input('sort', 'published_at');
        $source = $request->input('source');
        $channels = Channel::latest($sort)
            ->withCount('videos');
        if ($source !== null) {
            $channels->where('type', $source);
        }
        return view('channels.index', [
            'title' => __('Channels'),
            'channels' => $channels->paginate(35)->withQueryString(),
            'sort' => $sort,
            'source' => $source,
        ]);
    }

    public function videos(Channel $channel)
    {
        $videos = $channel->videos()
            ->withCount('files')
            ->latest('published_at');
        return view('channels.videos', [
            'title' => $channel->title,
            'channel' => $channel,
            'videos' => $videos->paginate(24),
        ]);
    }

    public function playlists(Channel $channel)
    {
        if (!source($channel->type)->playlist()) {
            abort(404);
        }
        $playlists = $channel->playlists()
            ->with(['firstItem', 'firstItem.video'])
            ->withCount('items')
            ->latest('published_at');
        return view('channels.playlists', [
            'title' => $channel->title,
            'channel' => $channel,
            'playlists' => $playlists->get(),
        ]);
    }

    public function search(Channel $channel, Request $request)
    {
        // TODO: show video and playlists results in single list
        $videos = Video::search($request->input('q'))
            ->query(function ($builder): void {
                $builder->withCount('files');
            })
            ->where('channel_id', $channel->id);
        $playlists = Playlist::search($request->input('q'))
            ->query(function ($builder): void {
                $builder
                    ->with('firstItem', 'firstItem.video')
                    ->withCount('items');
            })
            ->where('channel_id', $channel->id);
        return view('channels.search', [
            'title' => $channel->title,
            'channel' => $channel,
            'videos' => $videos->paginate(24),
            'playlists' => $playlists->get(),
            'channelQ' => $request->input('q'),
        ]);
    }

    public function about(Channel $channel)
    {
        return view('channels.about', [
            'title' => $channel->title,
            'channel' => $channel,
        ]);
    }
}
