<?php

namespace App\Sources\YouTube\Source;

use App\Exceptions\ImportException;
use App\Models\Channel;
use App\Sources\SourceChannel;
use App\Sources\YtDlp\YtDlpClient;
use App\Sources\YouTube\YouTubeClient;
use App\Traits\DownloadsImages;

class YouTubeChannel implements SourceChannel
{
    use DownloadsImages;

    public function getField(): string
    {
        return 'uuid';
    }

    public function import(string $id): Channel
    {
        $metadata = [];
        if ($id[0] === '@') {
            $metadata['username'] = substr($id, 1);
            $channel = Channel::where('metadata->username', $metadata['username'])
                ->where('type', 'youtube')
                ->first();
            if ($channel) {
                return $channel;
            }
        }

        if (YouTubeClient::isConfigured()) {
            $channelData = YouTubeClient::getChannelData($id);

            if ($md = $channelData['thumbnails']->getMedium()) {
                $imageUrl = $this->downloadImage($md->url, "thumbs/youtube-channel/{$id}.jpg");
            }
            if ($lg = $channelData['thumbnails']->getMaxres()) {
                $imageUrlLg = $this->downloadImage($lg->url, "thumbs/youtube-channel-lg/{$id}.jpg");
            }

            return Channel::create([
                'uuid' => $channelData['id'],
                'title' => $channelData['title'],
                'description' => $channelData['description'],
                'custom_url' => $channelData['custom_url'],
                'country' => $channelData['country'],
                'type' => 'youtube',
                'image_url' => $imageUrl ?? null,
                'image_url_lg' => $imageUrlLg ?? null,
                'published_at' => $channelData['published_at'],
                'metadata' => $metadata,
            ]);
        }

        $ytdl = new YtDlpClient();
        if (!$ytdl->isAvailable()) {
            throw new ImportException('YouTube API is not configured and yt-dlp is not available.');
        }

        $channelData = $ytdl->getChannelMetadata($this->buildImportUrl($id));
        $username = ltrim((string) ($channelData['uploader_id'] ?? $metadata['username'] ?? ''), '@');
        if ($username !== '') {
            $metadata['username'] = $username;
        }

        $thumbnail = YtDlpClient::getThumbnailUrl($channelData, 320);
        if ($thumbnail !== null) {
            $imageUrl = $this->downloadImage($thumbnail, "thumbs/youtube-channel/{$id}.jpg");
        }
        $poster = YtDlpClient::getThumbnailUrl($channelData);
        if ($poster !== null) {
            $imageUrlLg = $this->downloadImage($poster, "thumbs/youtube-channel-lg/{$id}.jpg");
        }

        $channelUuid = $channelData['channel_id'] ?? $channelData['id'] ?? null;
        if (!is_string($channelUuid) || $channelUuid === '') {
            throw new ImportException('yt-dlp did not return a YouTube channel ID.');
        }

        return Channel::create([
            'uuid' => $channelUuid,
            'title' => (string) (
                $channelData['channel']
                ?? $channelData['uploader']
                ?? $channelData['title']
                ?? $channelUuid
            ),
            'description' => (string) ($channelData['description'] ?? ''),
            'custom_url' => $username !== '' ? $username : null,
            'country' => null,
            'type' => 'youtube',
            'image_url' => $imageUrl ?? null,
            'image_url_lg' => $imageUrlLg ?? null,
            'published_at' => YtDlpClient::getPublishedAt($channelData),
            'metadata' => $metadata,
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/https?:\/\/(www\.)?(youtube\.com|youtu\.be)\/@([0-9a-z]{8,})/i', $url, $matches)) {
            return '@' . $matches[3];
        }
        if (preg_match('/https?:\/\/(www\.)?(youtube\.com|youtu\.be)\/channel\/([0-9a-z]{8,})/i', $url, $matches)) {
            return $matches[3];
        }
        return null;
    }

    public function getSourceUrl(Channel $channel): string
    {
        if (!empty($channel->metadata['username'])) {
            return 'https://www.youtube.com/@' . $channel->metadata['username'];
        }
        return 'https://www.youtube.com/channel/' . $channel->uuid;
    }

    protected function buildImportUrl(string $id): string
    {
        if ($id[0] === '@') {
            return 'https://www.youtube.com/' . $id;
        }

        return 'https://www.youtube.com/channel/' . $id;
    }
}
