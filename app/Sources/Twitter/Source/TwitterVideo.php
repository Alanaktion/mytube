<?php

namespace App\Sources\Twitter\Source;

use App\Exceptions\ImportException;
use App\Models\Channel;
use App\Models\Video;
use App\Sources\SourceVideo;
use App\Sources\Twitter\TwitterClient;
use App\Sources\YtDlp\YtDlpClient;
use App\Traits\DownloadsImages;
use Illuminate\Support\Str;

class TwitterVideo implements SourceVideo
{
    use DownloadsImages;

    public function canonicalizeId(string $id): string
    {
        return $id;
    }

    public function import(string $id): Video
    {
        if (TwitterClient::isConfigured()) {
            $twitter = new TwitterClient();
            $data = $twitter->getStatus($id);

            if (!empty($data->errors)) {
                throw new ImportException($data->errors[0]->message);
            }

            if (!empty($data->entities) && !empty($data->entities->media)) {
                $media = $data->entities->media[0];
                $url = substr((string) $media->media_url_https, 0, -4);

                $file = 'thumbs/twitter/' . basename($url) . '-small.jpg';
                $params = [
                    'format' => 'jpg',
                    'name' => 'small',
                ];
                $thumbnailUrl = $this->downloadImage($url . '?' . http_build_query($params), $file);

                $file = 'thumbs/twitter/' . basename((string) $media->media_url_https);
                $posterUrl = $this->downloadImage($url . '?' . http_build_query($params), $file);
            }

            $durationMs = $data->extended_entities->media[0]->video_info->duration_millis ?? null;
            $channel = Channel::import('twitter', $data->user->screen_name);
            return $channel->videos()->create([
                'uuid' => $data->id_str,
                'title' => Str::limit($data->full_text, 80, '…'),
                'description' => $data->full_text,
                'source_type' => 'twitter',
                'duration' => $durationMs ? floor($durationMs / 1000) : null,
                'published_at' => $data->created_at,
                'thumbnail_url' => $thumbnailUrl ?? null,
                'poster_url' => $posterUrl ?? null,
            ]);
        }

        $ytdl = new YtDlpClient();
        if (!$ytdl->isAvailable()) {
            throw new ImportException('Twitter API is not configured and yt-dlp is not available.');
        }

        $data = $ytdl->getVideoMetadata($this->getSourceUrlFromId($id));
        $thumbnail = YtDlpClient::getThumbnailUrl($data, 320);
        $poster = YtDlpClient::getThumbnailUrl($data);
        $channelImportId = (string) ($data['uploader_id'] ?? $data['channel_id'] ?? $data['display_id'] ?? '');
        if ($channelImportId === '') {
            throw new ImportException('yt-dlp did not return a Twitter account reference.');
        }

        $description = (string) ($data['description'] ?? $data['title'] ?? '');
        $channel = Channel::import('twitter', $channelImportId);
        return $channel->videos()->create([
            'uuid' => (string) ($data['id'] ?? $id),
            'title' => Str::limit($description, 80, '…'),
            'description' => $description,
            'source_type' => 'twitter',
            'duration' => isset($data['duration']) ? (int) round((float) $data['duration']) : null,
            'published_at' => YtDlpClient::getPublishedAt($data),
            'thumbnail_url' => $thumbnail ? $this->downloadImage($thumbnail, 'thumbs/twitter') : null,
            'poster_url' => $poster ? $this->downloadImage($poster, 'thumbs/twitter') : null,
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        if (preg_match('/^(https?:\/\/)?(www\.)?twitter\.com\/([^\/]+)\/status\/(\d{18,19})/i', $url, $matches)) {
            return $matches[4];
        }
        return null;
    }

    public function matchId(string $id): bool
    {
        return (bool)preg_match('/^\d{18,19}$/i', $id);
    }

    public function matchFilename(string $filename): ?string
    {
        if (preg_match('/-(\d{18,19})\.(mp4|m4v|avi|mkv|webm)$/i', $filename, $matches)) {
            return $matches[1];
        }
        if (preg_match('/ \[(\d{18,19})\]\.(mp4|m4v|avi|mkv|webm)$/i', $filename, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function getSourceUrl(Video $video): string
    {
        return 'https://twitter.com/' . $video->channel->custom_url . '/status/' . $video->uuid;
    }

    public function getEmbedHtml(Video $video): ?string
    {
        return view('sources.embed-twitter', ['video' => $video])->render();
    }

    protected function getSourceUrlFromId(string $id): string
    {
        return 'https://twitter.com/i/status/' . $id;
    }
}
