<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function showVideoThumb(Video $video)
    {
        if (!$video->downloadThumbs()) {
            return abort(404);
        }
        return redirect()->to(Storage::url("thumbs/youtube/{$video->uuid}.jpg"), 301);
    }

    public function showVideoPoster(Video $video)
    {
        if (!$video->downloadThumbs()) {
            return abort(404);
        }
        return redirect()->to(Storage::url("thumbs/youtube-maxres/{$video->uuid}.jpg"), 301);
    }

    public function showChannel(Channel $channel)
    {
        // TODO: implement channel profile image fetching
    }
}
