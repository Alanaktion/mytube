<?php

namespace Tests\Feature;

use App\Models\Playlist;
use Tests\TestCase;

class PlaylistsTest extends TestCase
{
    public function testIndex()
    {
        $response = $this->get('/playlists');
        $response->assertStatus(200);
    }

    public function testShow()
    {
        $playlist = Playlist::first();
        $response = $this->get("/playlists/{$playlist->uuid}");
        $response->assertStatus(200);
    }

    public function test404()
    {
        $response = $this->get("/playlists/invalid-uuid");
        $response->assertStatus(404);
    }
}
