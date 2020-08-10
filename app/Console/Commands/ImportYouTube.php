<?php

namespace App\Console\Commands;

use App\Models\Channel;
use App\Models\Video;
use Exception;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\OutputInterface;

class ImportYouTube extends Command
{
    protected $signature = 'youtube:import-fs {directory}';

    protected $description = 'Import YouTube videos from the filesystem.';

    protected $youtube;

    public function __construct()
    {
        parent::__construct();

        $client = new Google_Client();
        $client->setApplicationName('API code samples');
        $client->setScopes([
            'https://www.googleapis.com/auth/youtube.readonly',
        ]);
        $client->setDeveloperKey(config('services.youtube.key'));
        $this->youtube = new Google_Service_YouTube($client);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $verbosity = $this->getOutput()->getVerbosity();
        $directory = $this->argument('directory');
        if (!is_dir($directory)) {
            $this->error('The specified directory does not exist.');
            return 1;
        }

        $this->line('Scanning directory...');
        $cwd = getcwd();
        chdir($directory);
        $files = glob('*.*') + glob('**/*.*');
        $videos = [];
        foreach ($files as $file) {
            // Match filename pattern from youtube-dl
            // Long-term this should be made better somehow
            if (preg_match('/-([A-Za-z0-9_\-]{11})\.(mp4|m4v|avi|mkv|webm)$/', $file, $matches)) {
                $videos[$matches[1]] = $file;
            } elseif ($verbosity >= OutputInterface::VERBOSITY_VERBOSE) {
                $this->warn('Unmatched file: ' . $file);
            }
        }
        chdir($cwd);

        $videoCount = count($videos);
        $this->info("Importing $videoCount videos...");

        $bar = $this->output->createProgressBar($videoCount);
        $bar->start();
        foreach ($videos as $id => $file) {
            $path = realpath($directory . DIRECTORY_SEPARATOR . $file);
            try {
                $this->importVideo($id, $path);
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
            $bar->advance();
        }
        $bar->finish();

        return 0;
    }

    /**
     * Retrieve and store video metadata if it doesn't already exist
     */
    protected function importVideo(string $id, string $filePath): Video
    {
        if ($video = Video::where('uuid', $id)->first()) {
            return $video;
        }

        $data = $this->getVideoData($id);

        $channel = Channel::where('uuid', $data['channel_id'])->first();
        if (!$channel) {
            $channelData = $this->getChannelData($data['channel_id']);
            $channel = Channel::create([
                'uuid' => $channelData['id'],
                'title' => $channelData['title'],
                'description' => $channelData['description'],
                'custom_url' => $channelData['custom_url'],
                'country' => $channelData['country'],
                'type' => 'youtube',
                'published_at' => $channelData['published_at'],
            ]);
        }

        return $channel->videos()->create([
            'uuid' => $data['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'published_at' => $data['published_at'],
            'file_path' => $filePath,
        ]);
    }

    /**
     * Get metadata for a video from YouTube by video ID
     *
     * @link https://developers.google.com/youtube/v3/docs/videos
     */
    protected function getVideoData(string $id): array
    {
        $response = $this->youtube->videos->listVideos('snippet', [
            'id' => $id,
        ]);
        usleep(1e5);
        foreach ($response as $video) {
            /** @var \Google_Service_YouTube_Video $video */
            return [
                'id' => $video->id,
                'channel_id' => $video->getSnippet()->channelId,
                'title' => $video->getSnippet()->title,
                'description' => $video->getSnippet()->description,
                'published_at' => $video->getSnippet()->publishedAt,
            ];
        }
        throw new Exception('Video not found');
    }

    /**
     * Get metadata for a channel from YouTube by channel ID
     *
     * @link https://developers.google.com/youtube/v3/docs/channels
     */
    protected function getChannelData(string $id): array
    {
        $response = $this->youtube->channels->listChannels('snippet', [
            'id' => $id,
        ]);
        usleep(1e5);
        foreach ($response as $channel) {
            /** @var \Google_Service_YouTube_Channel $channel */
            return [
                'id' => $channel->id,
                'title' => $channel->getSnippet()->title,
                'description' => $channel->getSnippet()->description,
                'custom_url' => $channel->getSnippet()->customUrl,
                'country' => $channel->getSnippet()->country,
                'published_at' => $channel->getSnippet()->publishedAt,
            ];
        }
        throw new Exception('Channel not found');
    }
}
