<?php

namespace Tests\Feature;

use App\Models\Channel;
use Tests\TestCase;

class ChannelsTest extends TestCase
{
    public function testIndex()
    {
        $response = $this->get('/channels');
        $response->assertStatus(200);
    }

    public function testShow()
    {
        $channel = Channel::first();
        $response = $this->get("/channels/{$channel->uuid}");
        $response->assertStatus(200);
    }

    public function testShowPlaylists()
    {
        $channel = Channel::first();
        $response = $this->get("/channels/{$channel->uuid}/playlists");
        $response->assertStatus(200);
    }

    public function testShowAbout()
    {
        $channel = Channel::first();
        $response = $this->get("/channels/{$channel->uuid}/about");
        $response->assertStatus(200);
    }

    public function test404()
    {
        $response = $this->get("/channels/invalid-uuid");
        $response->assertStatus(404);
    }
}
