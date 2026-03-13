<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Playlist;
use App\Models\User;
use App\Models\Video;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    public function testFavoritesRequireAuthentication(): void
    {
        $response = $this->get('/favorites');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function testFavoritesIndexForAuthenticatedUser(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/favorites');

        $response->assertStatus(200);
    }

    public function testToggleVideoAddsFavorite(): void
    {
        $user = User::factory()->create();
        $video = Video::first();

        $response = $this->actingAs($user)->postJson('/favorites/toggleVideo', [
            'uuid' => $video->uuid,
            'value' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('uuid', $video->uuid);
        $response->assertJsonPath('value', true);
        $this->assertDatabaseHas('user_favorite_videos', [
            'user_id' => $user->id,
            'video_id' => $video->id,
        ]);
    }

    public function testToggleVideoRemovesFavorite(): void
    {
        $user = User::factory()->create();
        $video = Video::first();
        $user->favoriteVideos()->attach($video->id);

        $response = $this->actingAs($user)->postJson('/favorites/toggleVideo', [
            'uuid' => $video->uuid,
            'value' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('value', false);
        $this->assertDatabaseMissing('user_favorite_videos', [
            'user_id' => $user->id,
            'video_id' => $video->id,
        ]);
    }

    public function testTogglePlaylistAddsFavorite(): void
    {
        $user = User::factory()->create();
        $playlist = Playlist::first();

        $response = $this->actingAs($user)->postJson('/favorites/togglePlaylist', [
            'uuid' => $playlist->uuid,
            'value' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('uuid', $playlist->uuid);
        $this->assertDatabaseHas('user_favorite_playlists', [
            'user_id' => $user->id,
            'playlist_id' => $playlist->id,
        ]);
    }

    public function testToggleChannelAddsFavorite(): void
    {
        $user = User::factory()->create();
        $channel = Channel::first();

        $response = $this->actingAs($user)->postJson('/favorites/toggleChannel', [
            'uuid' => $channel->uuid,
            'value' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('uuid', $channel->uuid);
        $this->assertDatabaseHas('user_favorite_channels', [
            'user_id' => $user->id,
            'channel_id' => $channel->id,
        ]);
    }

    public function testToggleVideoValidatesUuid(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/favorites/toggleVideo', [
            'uuid' => 'not-a-real-video',
            'value' => true,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['uuid']);
    }
}
