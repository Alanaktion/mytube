<?php

namespace App\Sources\YouTube\Source;

use App\Exceptions\ImportException;
use App\Models\Channel;
use App\Models\Video;
use App\Sources\SourceVideo;
use App\Sources\YtDlp\YtDlpClient;
use App\Sources\YouTube\YouTubeClient;
use App\Traits\DownloadsImages;
use DateInterval;

class YouTubeVideo implements SourceVideo
{
    use DownloadsImages;

    public function import(string $id): Video
    {
        if (YouTubeClient::isConfigured()) {
            $data = YouTubeClient::getVideoData($id);

            $channel = Channel::import('youtube', $data['channel_id']);
            return $channel->videos()->create([
                'uuid' => $data['id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'source_type' => 'youtube',
                'source_visibility' => $data['visibility'],
                'duration' => $this->formatDuration($data['duration']),
                'is_livestream' => $data['is_livestream'],
                'published_at' => $data['published_at'],
                'thumbnail_url' => $this->downloadImage(
                    "https://img.youtube.com/vi/{$id}/hqdefault.jpg",
                    "thumbs/youtube/{$id}.jpg"
                ),
                'poster_url' => $this->downloadImage(
                    "https://img.youtube.com/vi/{$id}/maxresdefault.jpg",
                    "thumbs/youtube-maxres/{$id}.jpg"
                ),
            ]);
        }

        $ytdl = new YtDlpClient();
        if (!$ytdl->isAvailable()) {
            throw new ImportException('YouTube API is not configured and yt-dlp is not available.');
        }

        $data = $ytdl->getVideoMetadata($this->getSourceUrlFromId($id));
        $channelImportId = $data['channel_id'] ?? null;
        if ((!is_string($channelImportId) || $channelImportId === '') && !empty($data['uploader_id'])) {
            $channelImportId = '@' . ltrim((string) $data['uploader_id'], '@');
        }
        if (!is_string($channelImportId) || $channelImportId === '') {
            throw new ImportException('yt-dlp did not return a YouTube channel reference.');
        }

        $thumbnail = YtDlpClient::getThumbnailUrl($data, 320);
        $poster = YtDlpClient::getThumbnailUrl($data);

        $channel = Channel::import('youtube', $channelImportId);
        return $channel->videos()->create([
            'uuid' => (string) ($data['id'] ?? $id),
            'title' => (string) ($data['title'] ?? $id),
            'description' => (string) ($data['description'] ?? ''),
            'source_type' => 'youtube',
            'source_visibility' => $this->normalizeVisibility($data),
            'duration' => isset($data['duration']) ? (int) round((float) $data['duration']) : null,
            'is_livestream' => !empty($data['is_live']),
            'published_at' => YtDlpClient::getPublishedAt($data),
            'thumbnail_url' => $thumbnail ? $this->downloadImage($thumbnail, "thumbs/youtube/{$id}.jpg") : null,
            'poster_url' => $poster ? $this->downloadImage($poster, "thumbs/youtube-maxres/{$id}.jpg") : null,
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        $pattern = '/^(https:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([0-9A-Za-z_\-]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[4];
        }
        return null;
    }

    public function canonicalizeId(string $id): string
    {
        return $id;
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
        if (preg_match('/ \[([A-Za-z0-9_\-]{11})\]\.(mp4|m4v|avi|mkv|webm)$/', $filename, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function getSourceUrl(Video $video): string
    {
        return 'https://www.youtube.com/watch?v=' . $video->uuid;
    }

    public function getEmbedHtml(Video $video): ?string
    {
        return view('sources.embed-youtube', ['video' => $video])->render();
    }

    /**
     * Convert ISO 8601 duration to seconds.
     */
    protected function formatDuration(?string $duration): ?int
    {
        if (!$duration) {
            return null;
        }
        $interval = new DateInterval($duration);
        return $interval->d * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s;
    }

    protected function getSourceUrlFromId(string $id): string
    {
        return 'https://www.youtube.com/watch?v=' . $id;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function normalizeVisibility(array $data): string
    {
        return match (strtolower((string) ($data['availability'] ?? 'public'))) {
            Video::VISIBILITY_PRIVATE => Video::VISIBILITY_PRIVATE,
            Video::VISIBILITY_UNLISTED => Video::VISIBILITY_UNLISTED,
            default => Video::VISIBILITY_PUBLIC,
        };
    }
}
