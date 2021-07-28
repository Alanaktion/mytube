<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Video;
use Tests\TestCase;

class AdminTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * Set up the administrator user.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testLoginRedirect(): void
    {
        $response = $this->get('/admin');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function testDashboard(): void
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin');
        $response->assertStatus(200);
    }

    public function testMissingIndex(): void
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin/missing');
        $response->assertStatus(200);

        // Check that the latest missing video is included on the page
        $video = Video::whereNull('file_path')->latest('id')->first();
        /** @var \Illuminate\Database\Eloquent\Collection $videoCollection */
        $videoCollection = $response->viewData('videos');
        $this->assertTrue($videoCollection->contains($video));
    }
}
