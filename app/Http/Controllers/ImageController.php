<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Video;
use App\Sources\YouTube\YouTubeClient;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageController extends Controller
{
    /**
     * @deprecated Sources should download all images when importing videos.
     */
    public function showVideoThumb(Video $video)
    {
        if ($video->thumbnail_url) {
            return redirect()->to($video->thumbnail_url, 301);
        }

        $video->load('channel:id,type');

        // Download a thumbnail from the source
        $disk = Storage::disk('public');
        if ($video->source_type == 'youtube') {
            $exists = false;
            if ($disk->exists("thumbs/youtube/{$video->uuid}.jpg")) {
                $exists = true;
            } else {
                try {
                    $data = file_get_contents("https://img.youtube.com/vi/{$video->uuid}/hqdefault.jpg");
                    $disk->put("thumbs/youtube/{$video->uuid}.jpg", $data, 'public');
                    $exists = true;
                } catch (\Exception) {
                    //
                }
            }
            if ($exists) {
                $video->thumbnail_url = Storage::url("public/thumbs/youtube/{$video->uuid}.jpg");
                $video->save();
                return redirect()->to($video->thumbnail_url, 301);
            }
        }

        // Generate a thumbnail
        $generated = $disk->exists("thumbs/generated/{$video->uuid}@360p.jpg");
        if (!$generated) {
            $generated = $this->generateImage($video, 480, 360, '@360p');
        }
        if ($generated) {
            $video->thumbnail_url = Storage::url("public/thumbs/generated/{$video->uuid}@360p.jpg");
            $video->save();
            return redirect()->to($video->thumbnail_url, 301);
        }

        abort(404);
    }

    /**
     * @deprecated Sources should download all images when importing videos.
     */
    public function showVideoPoster(Video $video)
    {
        if ($video->poster_url) {
            return redirect()->to($video->poster_url, 301);
        }

        $video->load('channel:id,type');

        // Download a thumbnail from the source
        $disk = Storage::disk('public');
        if ($video->source_type == 'youtube') {
            $exists = false;
            if ($disk->exists("thumbs/youtube-maxres/{$video->uuid}.jpg")) {
                $exists = true;
            } else {
                try {
                    $data = file_get_contents("https://img.youtube.com/vi/{$video->uuid}/maxresdefault.jpg");
                    $disk->put("thumbs/youtube-maxres/{$video->uuid}.jpg", $data, 'public');
                    $exists = true;
                } catch (\Exception) {
                    //
                }
            }
            if ($exists) {
                $video->poster_url = Storage::url("public/thumbs/youtube-maxres/{$video->uuid}.jpg");
                $video->save();
                return redirect()->to($video->poster_url, 301);
            }
        }

        // Generate a thumbnail
        $generated = $disk->exists("thumbs/generated/{$video->uuid}@720p.jpg");
        if (!$generated) {
            $generated = $this->generateImage($video, 1280, 720, '@720p');
        }
        if ($generated) {
            $video->poster_url = Storage::url("public/thumbs/generated/{$video->uuid}@720p.jpg");
            $video->save();
            return redirect()->to($video->poster_url, 301);
        }

        abort(404);
    }

    /**
     * @deprecated Sources should download all images when importing channels.
     */
    public function showChannel(Channel $channel)
    {
        if ($channel->image_url) {
            return redirect()->to($channel->image_url, 301);
        }

        $disk = Storage::disk('public');
        if ($channel->type == 'youtube') {
            $channelData = YouTubeClient::getChannelData($channel->uuid);
            try {
                if ($md = $channelData['thumbnails']->getMedium()) {
                    $data = file_get_contents($md->url);
                    $disk->put("thumbs/youtube-channel/{$channel->uuid}.jpg", $data, 'public');
                    $channel->image_url = Storage::url("public/thumbs/youtube-channel/{$channel->uuid}.jpg");
                }
                if ($lg = $channelData['thumbnails']->getMedium()) {
                    $data = file_get_contents($lg->url);
                    $disk->put("thumbs/youtube-channel-lg/{$channel->uuid}.jpg", $data, 'public');
                    $channel->image_url_lg = Storage::url("public/thumbs/youtube-channel-lg/{$channel->uuid}.jpg");
                }
                $channel->save();
            } catch (\Exception) {
                //
            }
        }

        if ($channel->image_url) {
            return redirect()->to($channel->image_url, 301);
        }

        abort(404);
    }

    /**
     * Generate an image from a video's local file using ffmpeg
     */
    protected function generateImage(Video $video, int $w, int $h, string $suffix = ''): bool
    {
        $video->load('files:id,video_id,path');
        if ($video->files->isEmpty()) {
            return false;
        }
        /** @var \App\Models\VideoFile $file */
        $file = $video->files->first();
        if (!is_file($file->path)) {
            return false;
        }

        // Determine video duration
        $path = escapeshellarg($file->path);
        $duration = shell_exec("ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $path 2>/dev/null");
        if ($duration) {
            $duration = (float)trim($duration);
        }

        // Seek to 30% of video duration, or 10 seconds if duration is unknown.
        $seconds = $duration ? floor($duration * 0.30) : 10;
        $framePath = storage_path("app/{$video->uuid}-frame.png");
        shell_exec("ffmpeg -ss $seconds -i $path -vframes 1 -vcodec png -an -y " . escapeshellarg($framePath));

        Storage::makeDirectory('public/thumbs/generated');

        // Generate resampled + cropped image
        $thumb = Image::make($framePath)->resize($w, $h, function ($constraint): void {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->resizeCanvas($w, $h, 'center', false, '#000');
        $thumb->save(storage_path("app/public/thumbs/generated/{$video->uuid}{$suffix}.jpg"));

        unlink($framePath);

        return true;
    }
}
