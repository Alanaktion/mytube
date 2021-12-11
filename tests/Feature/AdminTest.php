<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Video;
use Tests\TestCase;

class AdminTest extends TestCase
{
    /** @var User */
    protected $user;
    /** @var User */
    protected $admin;

    /**
     * Set up the test users.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function testLoginRedirect(): void
    {
        $response = $this->get('/admin');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function testDashboard(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin');
        $response->assertStatus(200);
    }

    public function testDashboardUnauthorized(): void
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin');
        $response->assertStatus(403);
    }

    public function testMissingIndex(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin/missing');
        $response->assertStatus(200);

        // Check that the latest missing video is included on the page
        $video = Video::whereNull('file_path')->latest('created_at')->first();
        /** @var \Illuminate\Database\Eloquent\Collection $videoCollection */
        $videoCollection = $response->viewData('videos');
        $this->assertTrue($videoCollection->contains($video));
    }
}
