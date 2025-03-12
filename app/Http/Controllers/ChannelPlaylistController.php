<?php

namespace App\Http\Controllers;

use App\Models\Channel;

class ChannelPlaylistController extends Controller
{
    public function index(Channel $channel)
    {
        if (!source($channel->type)->playlist() instanceof \App\Sources\SourcePlaylist) {
            abort(404);
        }
        $playlists = $channel->playlists()
            ->with(['firstItem', 'firstItem.video'])
            ->withCount('items')
            ->withDuration()
            ->latest('published_at')
            ->latest('id');
        return view('channels.playlists', [
            'title' => $channel->title,
            'channel' => $channel,
            'playlists' => $playlists->get(),
        ]);
    }
}
