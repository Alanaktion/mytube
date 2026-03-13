<?php

namespace Tests\Feature;

use App\Models\VideoFile;
use Tests\TestCase;

class VideoFileUrlTest extends TestCase
{
    public function testUrlUsesConfiguredExternalMapping(): void
    {
        config()->set('filesystems.video_file_url_mappings', [[
            'filesystem_prefix' => '/storage/Videos',
            'url_prefix' => 'https://videos.example.com/~user/videos/',
        ]]);

        $videoFile = new VideoFile([
            'path' => '/storage/Videos/example.mp4',
        ]);

        $this->assertSame(
            'https://videos.example.com/~user/videos/example.mp4',
            $videoFile->url,
        );
    }

    public function testUrlFallsBackToDefaultStorageUrlWhenNoMappingMatches(): void
    {
        config()->set('filesystems.video_file_url_mappings', [[
            'filesystem_prefix' => '/storage/Videos',
            'url_prefix' => 'https://videos.example.com/~user/videos',
        ]]);

        $path = '/storage/Other/example.mp4';
        $videoFile = new VideoFile([
            'path' => $path,
        ]);

        $hashedPath = sha1($path) . '.mp4';
        $hashedPath = "{$hashedPath[0]}/{$hashedPath[1]}/$hashedPath";

        $this->assertSame(url("/storage/videos/$hashedPath"), $videoFile->url);
    }

    public function testUrlUsesTheMatchingEntryFromMultipleMappings(): void
    {
        config()->set('filesystems.video_file_url_mappings', [
            [
                'filesystem_prefix' => '/mnt/archive',
                'url_prefix' => 'https://archive.example.com/videos',
            ],
            [
                'filesystem_prefix' => '/storage/Videos',
                'url_prefix' => 'https://videos.example.com/~user/videos',
            ],
        ]);

        $videoFile = new VideoFile([
            'path' => '/storage/Videos/subdir/example.mp4',
        ]);

        $this->assertSame(
            'https://videos.example.com/~user/videos/subdir/example.mp4',
            $videoFile->url,
        );
    }
}
