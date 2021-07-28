<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Video;
use Tests\TestCase;

class HomeTest extends TestCase
{
    public function testHome(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testSearchVideo(): void
    {
        $video = Video::first();
        $query = $video->title;
        $response = $this->get('/search?q=' . urlencode($query));
        $response->assertStatus(200);

        // Check that the video is in the result list
        /** @var \Illuminate\Database\Eloquent\Collection $videoCollection */
        $videoCollection = $response->viewData('videos');
        $this->assertTrue($videoCollection->contains($video));
    }

    public function testSearchChannel(): void
    {
        $channel = Channel::first();
        $query = $channel->title;
        $response = $this->get('/search?q=' . urlencode($query));
        $response->assertStatus(200);

        // Check that the channel is in the result list
        /** @var \Illuminate\Database\Eloquent\Collection $channelCollection */
        $channelCollection = $response->viewData('channels');
        $this->assertTrue($channelCollection->contains($channel));
    }
}
