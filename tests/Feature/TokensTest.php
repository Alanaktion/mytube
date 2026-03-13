<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class TokensTest extends TestCase
{
    public function testStoreRequiresAuthentication(): void
    {
        $response = $this->post('/tokens', [
            'name' => 'CLI',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function testStoreValidatesName(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tokens', [
            'name' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name']);
    }

    public function testStoreCreatesAccessToken(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tokens', [
            'name' => 'CI Token',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('message');

        $token = PersonalAccessToken::query()
            ->where('tokenable_type', User::class)
            ->where('tokenable_id', $user->id)
            ->where('name', 'CI Token')
            ->first();

        $this->assertNotNull($token);
    }

    public function testDestroyRevokesAccessToken(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('Revokable token');
        $tokenId = explode('|', $plainTextToken->plainTextToken)[0];

        $response = $this->actingAs($user)->delete("/tokens/{$tokenId}");

        $response->assertStatus(302);
        $response->assertSessionHas('message', 'Access token revoked.');
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }
}
