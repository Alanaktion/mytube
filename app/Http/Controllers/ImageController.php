<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function showVideoThumb(Video $video)
    {
        if ($video->channel->type != 'youtube') {
            return abort(404);
        }

        $disk = Storage::disk('public');
        $exists = $disk->exists("thumbs/youtube/{$video->uuid}.jpg");
        if (!$exists) {
            $data = file_get_contents("https://img.youtube.com/vi/{$video->uuid}/hqdefault.jpg");
            $disk->put("thumbs/youtube/{$video->uuid}.jpg", $data, 'public');

            $data = file_get_contents("https://img.youtube.com/vi/{$video->uuid}/maxresdefault.jpg");
            $disk->put("thumbs/youtube-maxres/{$video->uuid}.jpg", $data, 'public');
        }

        return redirect()->away("/thumbs/youtube/{$video->uuid}.jpg", 301);
    }

    public function showChannel(Channel $channel)
    {
        // TODO: implement channel profile image fetching
    }
}
