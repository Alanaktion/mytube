<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\JobDetail;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class JobDetailsTest extends TestCase
{
    public function testIndexRequiresAuthentication(): void
    {
        $response = $this->get('/job-details');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function testIndexReturnsJobDetails(): void
    {
        $user = User::factory()->create();
        $channel = Channel::first();

        JobDetail::query()->create([
            'type' => 'import',
            'model_type' => Channel::class,
            'model_id' => $channel->id,
            'data' => ['status' => 'ok'],
        ]);

        $response = $this->actingAs($user)->getJson('/job-details');

        $response->assertStatus(200);
        $response->assertJsonPath('0.model_type', Channel::class);
        $response->assertJsonPath('0.model_id', $channel->id);
    }

    public function testIndexFiltersByTypeAndId(): void
    {
        $user = User::factory()->create();
        $channel = Channel::factory()->create();
        $otherChannel = Channel::factory()->create();

        JobDetail::query()->create([
            'type' => 'import',
            'model_type' => Channel::class,
            'model_id' => $channel->id,
            'data' => ['job' => (string) Str::uuid()],
        ]);

        JobDetail::query()->create([
            'type' => 'import',
            'model_type' => Channel::class,
            'model_id' => $otherChannel->id,
            'data' => ['job' => (string) Str::uuid()],
        ]);

        $response = $this->actingAs($user)->getJson("/job-details?type=channel&id={$channel->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'model_type' => Channel::class,
            'model_id' => $channel->id,
        ]);
    }

    public function testIndexValidatesType(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/job-details?type=invalid');

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['type']);
    }
}
