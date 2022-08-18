<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessChannelImport;
use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Channel::class);
    }

    public function index(Request $request)
    {
        $request->validate([
            'sort' => ['sometimes', 'string', 'in:published_at,created_at'],
            'source' => ['sometimes', 'string', 'nullable'],
        ]);
        $sort = $request->input('sort', 'published_at');
        $source = $request->input('source');
        $channels = Channel::latest($sort)
            ->latest('id')
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

    public function show(Channel $channel)
    {
        return view('channels.about', [
            'title' => $channel->title,
            'channel' => $channel,
        ]);
    }

    public function update(Channel $channel, Request $request)
    {
        $request->validate([
            'playlists' => ['sometimes', 'boolean'],
            'videos' => ['sometimes', 'boolean'],
        ]);

        ProcessChannelImport::dispatch(
            $channel->type,
            $channel->uuid,
            $request->boolean('videos'),
            $request->boolean('playlists'),
        );

        $message = 'Channel import started.';
        if (config('queue.default') == 'sync') {
            $message = 'Channel imported.';
        }
        return redirect()->back()->with('message', $message);
    }
}
