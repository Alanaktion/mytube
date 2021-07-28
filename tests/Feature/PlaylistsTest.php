<?php

namespace Tests\Feature;

use App\Models\Playlist;
use Tests\TestCase;

class PlaylistsTest extends TestCase
{
    public function testIndex(): void
    {
        $response = $this->get('/playlists');
        $response->assertStatus(200);
    }

    public function testShow(): void
    {
        $playlist = Playlist::first();
        $response = $this->get("/playlists/{$playlist->uuid}");
        $response->assertStatus(200);
    }

    public function test404(): void
    {
        $response = $this->get("/playlists/invalid-uuid");
        $response->assertStatus(404);
    }
}
