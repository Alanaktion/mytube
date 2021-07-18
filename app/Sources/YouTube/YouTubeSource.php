<?php

namespace App\Sources\YouTube;

use App\Models\Channel;
use App\Models\ImportError;
use App\Models\Playlist;
use App\Models\Video;
use App\Sources\Source;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class YouTubeSource implements Source
{
    public function getSourceType(): string
    {
        return 'youtube';
    }

    public function getChannelField(): string
    {
        return 'uuid';
    }

    public function getDisplayName(): string
    {
        return 'YouTube';
    }

    public function getIcon(): View
    {
        return view('sources.icon-youtube');
    }

    public function canonicalizeVideo(string $id): string
    {
        return $id;
    }

    public function importChannel(string $id): Channel
    {
        $channelData = YouTubeClient::getChannelData($id);

        // Download images
        $disk = Storage::disk('public');
        $imageUrl = null;
        $imageUrlLg = null;
        if ($md = $channelData['thumbnails']->getMedium()) {
            $data = file_get_contents($md->url);
            $disk->put("thumbs/youtube-channel/{$id}.jpg", $data, 'public');
            $imageUrl = Storage::url("public/thumbs/youtube-channel/{$id}.jpg");
        }
        if ($lg = $channelData['thumbnails']->getMedium()) {
            $data = file_get_contents($lg->url);
            $disk->put("thumbs/youtube-channel-lg/{$id}.jpg", $data, 'public');
            $imageUrlLg = Storage::url("public/thumbs/youtube-channel-lg/{$id}.jpg");
        }

        // Create channel
        return Channel::create([
            'uuid' => $channelData['id'],
            'title' => $channelData['title'],
            'description' => $channelData['description'],
            'custom_url' => $channelData['custom_url'],
            'country' => $channelData['country'],
            'type' => $this->getSourceType(),
            'image_url' => $imageUrl,
            'image_url_lg' => $imageUrlLg,
            'published_at' => $channelData['published_at'],
        ]);
    }

    public function importVideo(string $id): Video
    {
        $data = YouTubeClient::getVideoData($id);

        // Download images
        $this->downloadImages($id);

        // Create video
        $channel = Channel::import($this->getSourceType(), $data['channel_id']);
        return $channel->videos()->create([
            'uuid' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'source_type' => 'youtube',
            'source_visibility' => $data['visibility'],
            'is_livestream' => $data['is_livestream'],
            'published_at' => $data['published_at'],
            'thumbnail_url' => Storage::url("public/thumbs/youtube/{$id}.jpg"),
            'poster_url' => Storage::url("public/thumbs/youtube/{$id}.jpg"),
        ]);
    }

    public function supportsPlaylists(): bool
    {
        return true;
    }

    public function importPlaylist(string $id): Playlist
    {
        $data = YouTubeClient::getPlaylistData($id);
        $channel = Channel::import($this->getSourceType(), $data['channel_id']);
        /** @var Playlist $playlist */
        return $channel->playlists()->create([
            'uuid' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'published_at' => $data['published_at'],
        ]);
    }

    public function importPlaylistItems(Playlist $playlist): void
    {
        $playlist->load('items:id,playlist_id,uuid');

        $items = YouTubeClient::getPlaylistItemData($playlist->uuid);
        foreach ($items as $item) {
            /** @var \Google_Service_YouTube_PlaylistItem $item */
            if ($playlist->items->firstWhere('uuid', $item->id)) {
                // Skip existing item
                continue;
            }

            $videoId = $item->getSnippet()->getResourceId()->videoId;
            try {
                $video = $this->importVideo($videoId);
                $playlist->items()->create([
                    'video_id' => $video->id,
                    'uuid' => $item->id,
                    'position' => $item->getSnippet()->position,
                ]);
            } catch (\Exception $e) {
                ImportError::updateOrCreate([
                    'uuid' => $videoId,
                    'type' => 'youtube',
                ], [
                    'reason' => $e->getMessage(),
                ]);
                Log::warning('Failed importing playlist item: ' . $videoId);
            }
        }
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([0-9A-Za-z_\-]{11})/i', $url, $matches)) {
            return $matches[4];
        }
        return null;
    }

    public function matchId(string $id): bool
    {
        return (bool)preg_match('/^[0-9A-Za-z_\-]{11}$/', $id);
    }

    public function matchFilename(string $filename): ?string
    {
        if (preg_match('/-([A-Za-z0-9_\-]{11})\.(mp4|m4v|avi|mkv|webm)$/', $filename, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Download the thumbnail and poster images for a video by video ID
     */
    public function downloadImages(string $id)
    {
        $disk = Storage::disk('public');

        if (!$disk->exists("thumbs/youtube/{$id}.jpg")) {
            $data = file_get_contents("https://img.youtube.com/vi/{$id}/hqdefault.jpg");
            $disk->put("thumbs/youtube/{$id}.jpg", $data, 'public');
        }

        if (!$disk->exists("thumbs/youtube-maxres/{$id}.jpg")) {
            $data = file_get_contents("https://img.youtube.com/vi/{$id}/maxresdefault.jpg");
            $disk->put("thumbs/youtube-maxres/{$id}.jpg", $data, 'public');
        }

        usleep(250e3);
    }
}
