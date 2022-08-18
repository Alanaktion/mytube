<?php

namespace App\Http\Controllers;

use App\Models\Channel;

class ChannelPlaylistController extends Controller
{
    public function index(Channel $channel)
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
}
