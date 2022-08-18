<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;

class ChannelSearchController extends Controller
{
    public function index(Channel $channel, Request $request)
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
}
