<?php

namespace Tests\Feature;

use Tests\TestCase;

class LangTest extends TestCase
{
    /**
     * Test switching the application language via query parameter.
     */
    public function testSwitchLanguage(): void
    {
        $response = $this->get('/?lang=es');
        $response->assertSee('lang="es"', false);
        $this->flushSession();
    }

    /**
     * Test that the language cannot be manually changed to an invalid value.
     */
    public function testSwitchLanguageInvalid(): void
    {
        $response = $this->get('/?lang=asdf');
        $response->assertSee('lang="en"', false);
        $this->flushSession();
    }

    /**
     * Test that the language can be changed via HTTP header
     */
    public function testSwitchLanguageHeader(): void
    {
        $response = $this->get('/', [
            'Accept-Language' => 'ja',
        ]);
        $response->assertSee('lang="ja"', false);
        $this->flushSession();
    }

    /**
     * Test that the language setting persists in the session.
     */
    public function testLanguageSessionPersistence(): void
    {
        $response = $this->get('/?lang=ja');
        $response->assertSee('lang="ja"', false);

        $response = $this->get('/videos');
        $response->assertSee('lang="ja"', false);
        $this->flushSession();
    }
}
