<?php

namespace App\Sources\Twitter;

use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Video;
use App\Sources\Source;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TwitterSource implements Source
{
    public function getSourceType(): string
    {
        return 'twitter';
    }

    public function getChannelField(): string
    {
        return 'custom_url';
    }

    public function getDisplayName(): string
    {
        return 'Twitter';
    }

    public function getIcon(): View
    {
        return view('sources.icon-twitter');
    }

    public function canonicalizeVideo(string $id): string
    {
        return $id;
    }

    public function importChannel(string $id): Channel
    {
        $twitter = new TwitterClient();
        $channelData = $twitter->getUser($id);

        // Download images
        $disk = Storage::disk('public');
        $data = file_get_contents($channelData->profile_image_url_https);
        $file = 'thumbs/twitter-user/' . basename($channelData->profile_image_url_https);
        $disk->put($file, $data, 'public');
        $imageUrl = Storage::url('public/' . $file);

        // Create channel
        return Channel::create([
            'uuid' => $channelData->id_str, // may want a Twitter prefix or something, this is an int
            'title' => $channelData->name,
            'description' => $channelData->description,
            'custom_url' => $channelData->screen_name,
            'type' => $this->getSourceType(),
            'image_url' => $imageUrl,
            'image_url_lg' => $imageUrl,
            'published_at' => $channelData->created_at,
        ]);
    }

    public function importVideo(string $id): Video
    {
        $twitter = new TwitterClient();
        $data = $twitter->getStatus($id);

        // Download images
        if ($data->entities && $data->entities->media) {
            $media = $data->entities->media[0];
            $url = substr($media->media_url_https, 0, -4);

            $disk = Storage::disk('public');
            $file = 'thumbs/twitter/' . basename($url) . '-small.jpg';
            $params = [
                'format' => 'jpg',
                'name' => 'small',
            ];
            $disk->put($file, file_get_contents($url . '?' . http_build_query($params)), 'public');
            $thumbnailUrl = Storage::url('public/' . $file);

            $file = 'thumbs/twitter/' . basename($media->media_url_https);
            $disk->put($file, file_get_contents($media->media_url_https), 'public');
            $posterUrl = Storage::url('public/' . $file);
        }

        // Create video
        $channel = Channel::import($this->getSourceType(), $data->user->screen_name);
        return $channel->videos()->create([
            'uuid' => $data->id_str,
            'title' => Str::limit($data->full_text, 80, '…'),
            'description' => $data->full_text,
            'source_type' => 'twitter',
            'published_at' => $data->created_at,
            'thumbnail_url' => $thumbnailUrl,
            'poster_url' => $posterUrl,
        ]);
    }

    public function supportsPlaylists(): bool
    {
        return false;
    }

    public function importPlaylist(string $id): Playlist
    {
        throw new Exception('Playlist import is not supported by this source.');
    }

    public function importPlaylistItems(Playlist $playlist): void
    {
        throw new Exception('Playlist import is not supported by this source.');
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https?:\/\/)?(www\.)?twitter\.com\/([^/]+)\/status\/([0-9]{19})/i', $url, $matches)) {
            return $matches[4];
        }
        return null;
    }

    public function matchId(string $id): bool
    {
        return (bool)preg_match('/^[0-9]{19}$/i', $id);
    }

    public function matchFilename(string $filename): ?string
    {
        if (preg_match('/-([0-9]{19})\.(mp4|m4v|avi|mkv|webm)$/i', $filename, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
