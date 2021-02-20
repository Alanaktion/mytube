<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    public function index()
    {
        $videos = Auth::user()->favoriteVideos()
            ->withPivot('created_at')
            ->orderBy('user_favorite_videos.created_at', 'desc')
            ->paginate(24);
        return view('favorites.index', [
            'videos' => $videos,
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
}
