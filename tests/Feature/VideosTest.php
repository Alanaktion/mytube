<?php

namespace Tests\Feature;

use App\Models\Video;
use Tests\TestCase;

class VideosTest extends TestCase
{
    public function testIndex()
    {
        $response = $this->get('/videos');
        $response->assertStatus(200);
    }

    public function testShow()
    {
        $video = Video::first();
        $response = $this->get("/videos/{$video->uuid}");
        $response->assertStatus(200);
    }

    public function test404()
    {
        $response = $this->get("/videos/invalid-uuid");
        $response->assertStatus(404);
    }
}
