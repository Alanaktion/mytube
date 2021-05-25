<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $videos = $user->favoriteVideos()
            ->with('channel:id,uuid,title')
            ->withPivot('created_at')
            ->orderBy('user_favorite_videos.created_at', 'desc')
            ->paginate(36);
        $playlists = $user->favoritePlaylists()
            ->with(['firstItem', 'firstItem.video'])
            ->withPivot('created_at')
            ->orderBy('user_favorite_playlists.created_at', 'desc')
            ->paginate(24);
        $channels = $user->favoriteChannels()
            ->withCount('videos')
            ->withPivot('created_at')
            ->orderBy('user_favorite_channels.created_at', 'desc')
            ->paginate(24);
        return view('favorites.index', [
            'videos' => $videos,
            'playlists' => $playlists,
            'channels' => $channels,
        ]);
    }

    public function toggleVideo(Request $request)
    {
        $request->validate([
            'uuid' => 'required|exists:videos',
            'value' => 'sometimes|boolean',
        ]);

        $video = Video::where('uuid', $request->input('uuid'))->first();
        $val = $request->boolean('value', true);
        $favorites = Auth::user()->favoriteVideos();

        if ($val) {
            $favorites->attach($video->id);
        } else {
            $favorites->detach($video->id);
        }

        return [
            'uuid' => $video->uuid,
            'value' => $val,
        ];
    }

    public function togglePlaylist(Request $request)
    {
        $request->validate([
            'uuid' => 'required|exists:playlists',
            'value' => 'sometimes|boolean',
        ]);

        $playlist = Playlist::where('uuid', $request->input('uuid'))->first();
        $val = $request->boolean('value', true);
        $favorites = Auth::user()->favoritePlaylists();

        if ($val) {
            $favorites->attach($playlist->id);
        } else {
            $favorites->detach($playlist->id);
        }

        return [
            'uuid' => $playlist->uuid,
            'value' => $val,
        ];
    }

    public function toggleChannel(Request $request)
    {
        $request->validate([
            'uuid' => 'required|exists:channels',
            'value' => 'sometimes|boolean',
        ]);

        $channel = Channel::where('uuid', $request->input('uuid'))->first();
        $val = $request->boolean('value', true);
        $favorites = Auth::user()->favoriteChannels();

        if ($val) {
            $favorites->attach($channel->id);
        } else {
            $favorites->detach($channel->id);
        }

        return [
            'uuid' => $channel->uuid,
            'value' => $val,
        ];
    }
}
