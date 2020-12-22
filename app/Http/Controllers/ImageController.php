<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageController extends Controller
{
    public function showVideoThumb(Video $video)
    {
        if ($video->thumbnail_url) {
            return redirect()->to($video->thumbnail_url, 301);
        }

        $video->load('channel:id,type');

        // Download a thumbnail from the source
        $disk = Storage::disk('public');
        if ($video->channel->type == 'youtube') {
            $exists = false;
            if ($disk->exists("thumbs/youtube/{$video->uuid}.jpg")) {
                $exists = true;
            } else {
                try {
                    $data = file_get_contents("https://img.youtube.com/vi/{$video->uuid}/hqdefault.jpg");
                    $disk->put("thumbs/youtube/{$video->uuid}.jpg", $data, 'public');
                    $exists = true;
                } catch (\Exception $e) {
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
        if ($disk->exists("thumbs/generated/{$video->uuid}@360p.jpg") || $this->generateImage($video, 480, 360, '@360p')) {
            $video->thumbnail_url = Storage::url("thumbs/generated/{$video->uuid}@360p.jpg");
            $video->save();
            return redirect()->to($video->thumbnail_url, 301);
        }

        return abort(404);
    }

    public function showVideoPoster(Video $video)
    {
        if ($video->poster_url) {
            return redirect()->to($video->poster_url, 301);
        }

        $video->load('channel:id,type');

        // Download a thumbnail from the source
        $disk = Storage::disk('public');
        if ($video->channel->type == 'youtube') {
            $exists = false;
            if ($disk->exists("thumbs/youtube-maxres/{$video->uuid}.jpg")) {
                $exists = true;
            } else {
                try {
                    $data = file_get_contents("https://img.youtube.com/vi/{$video->uuid}/maxresdefault.jpg");
                    $disk->put("thumbs/youtube-maxres/{$video->uuid}.jpg", $data, 'public');
                    $exists = true;
                } catch (\Exception $e) {
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
        if ($disk->exists("thumbs/generated/{$video->uuid}@720p.jpg") || $this->generateImage($video, 1280, 720, '@720p')) {
            $video->poster_url = Storage::url("thumbs/generated/{$video->uuid}@720p.jpg");
            $video->save();
            return redirect()->to($video->poster_url, 301);
        }

        return abort(404);
    }

    public function showChannel(Channel $channel)
    {
        // TODO: implement channel profile image fetching
    }

    /**
     * Generate an image from a video's local file using ffmpeg
     */
    protected function generateImage(Video $video, int $w, int $h, string $suffix = ''): bool
    {
        if (!$video->file_path || !is_file($video->file_path)) {
            return false;
        }

        // Determine video duration
        $path = escapeshellarg($video->file_path);
        $duration = trim(shell_exec("ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $path"));

        // Seek to 30% of video duration, or 10 seconds if duration is unknown.
        $seconds = $duration ? floor($duration * 0.30) : 10;
        $framePath = storage_path("{$video->uuid}-frame.png");
        shell_exec("ffmpeg -ss $seconds -i $path -vframes 1 -vcodec png -an -y " . escapeshellarg($framePath));

        Storage::makeDirectory('public/thumbs/generated');

        // Generate resampled + cropped image
        $thumb = Image::make($framePath)->resize($w, $h, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->resizeCanvas($w, $h, 'center', false, '#000');
        $thumb->save(storage_path("public/thumbs/generated/{$video->uuid}{$suffix}.jpg"));

        unlink($framePath);

        return true;
    }
}
