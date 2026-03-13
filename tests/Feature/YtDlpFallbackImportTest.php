<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Video;
use App\Sources\YtDlp\YtDlpClient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class YtDlpFallbackImportTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        config()->set('services.youtube.key', '');
        config()->set('twitch-api.client_id', '');
        config()->set('twitch-api.client_secret', '');
    }

    /**
     * These are optional network-backed tests.
     *
     * Run tests with RUN_OPTIONAL_YTDLP_FEATURE_TESTS=true
     *
     * @dataProvider channelImportCases
     */
    public function testChannelImportFallsBackToYtDlp(string $type, string $url): void
    {
        $this->skipUnlessOptionalImportsEnabled();
        $this->skipIfPlaceholderUrl($url);

        $source = source($type);
        $id = $source->channel()->matchUrl($url);

        $this->assertNotNull($id, 'Channel URL did not match the expected source parser.');

        $channel = Channel::import($type, $id);

        $this->assertSame($type, $channel->type);
        $this->assertNotSame('', trim($channel->uuid));
        $this->assertNotSame('', trim($channel->title));
        $this->assertNotNull($channel->custom_url);
        $this->assertNotNull($channel->image_url);
        $this->assertNotNull($channel->image_url_lg);
        $this->assertSame($source->channel()->getSourceUrl($channel), $channel->source_link);
    }

    /**
     * @dataProvider videoImportCases
     */
    public function testVideoImportFallsBackToYtDlp(string $type, string $url): void
    {
        $this->skipUnlessOptionalImportsEnabled();
        $this->skipIfPlaceholderUrl($url);

        $source = source($type);
        $id = $source->video()->matchUrl($url);

        $this->assertNotNull($id, 'Video URL did not match the expected source parser.');

        $video = Video::import($type, $id);

        $this->assertSame($type, $video->source_type);
        $this->assertNotSame('', trim($video->uuid));
        $this->assertNotSame('', trim($video->title));
        $this->assertNotNull($video->channel);
        $this->assertSame($type, $video->channel->type);
        $this->assertNotNull($video->thumbnail_url);
        $this->assertNotNull($video->published_at);
        $this->assertSame($source->video()->getSourceUrl($video), $video->source_link);
    }

    /**
     * @return array<string, array{type: string, url: string}>
     */
    public static function channelImportCases(): array
    {
        return [
            'youtube channel' => [
                'type' => 'youtube',
                'url' => 'https://www.youtube.com/channel/UCuAXFkgsw1L7xaCfnd5JJOw',
            ],
            // 'twitch channel' => [
            //     'type' => 'twitch',
            //     'url' => 'https://www.twitch.tv/REPLACE_ME',
            // ],
        ];
    }

    /**
     * @return array<string, array{type: string, url: string}>
     */
    public static function videoImportCases(): array
    {
        return [
            'youtube video' => [
                'type' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ],
            // 'twitch video' => [
            //     'type' => 'twitch',
            //     'url' => 'https://www.twitch.tv/videos/REPLACE_ME',
            // ],
        ];
    }

    protected function skipUnlessOptionalImportsEnabled(): void
    {
        if (!filter_var(env('RUN_OPTIONAL_YTDLP_FEATURE_TESTS', false), FILTER_VALIDATE_BOOL)) {
            $this->markTestSkipped('Set RUN_OPTIONAL_YTDLP_FEATURE_TESTS=true to run yt-dlp fallback feature tests.');
        }

        $ytdl = new YtDlpClient();
        if (!$ytdl->isAvailable()) {
            $this->markTestSkipped('yt-dlp is not installed or is not available in the test environment.');
        }
    }

    protected function skipIfPlaceholderUrl(string $url): void
    {
        if (str_contains($url, 'REPLACE_ME')) {
            $this->markTestSkipped('Replace placeholder URLs in YtDlpFallbackImportTest with real public source URLs.');
        }
    }
}
