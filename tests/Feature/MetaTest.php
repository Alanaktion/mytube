<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MetaTest extends TestCase
{
    public function testManifest(): void
    {
        $response = $this->get('/manifest.json');

        $response->assertStatus(200);
        $response->assertJsonPath('name', config('app.name'));
        $response->assertJsonPath('display', 'minimal-ui');
        $this->assertStringStartsWith('application/manifest+json', (string) $response->headers->get('Content-Type'));
    }

    public function testRobotsDisallowWhenDisabled(): void
    {
        Config::set('app.robots', false);

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertSeeText('Disallow: /');
        $this->assertStringStartsWith('text/plain', (string) $response->headers->get('Content-Type'));
    }

    public function testRobotsAllowWhenEnabled(): void
    {
        Config::set('app.robots', true);

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertSeeText('Disallow:');
        $response->assertDontSeeText('Disallow: /');
    }

    public function testOpenSearch(): void
    {
        $response = $this->get('/opensearch.xml');

        $response->assertStatus(200);
        $response->assertSee('<OpenSearchDescription', false);
        $this->assertStringStartsWith('application/xml', (string) $response->headers->get('Content-Type'));
    }
}
