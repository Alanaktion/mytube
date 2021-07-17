<?php

namespace Tests\Feature;

use Tests\TestCase;

class LangTest extends TestCase
{
    /**
     * Test switching the application language via query parameter.
     *
     * @return void
     */
    public function testSwitchLanguage()
    {
        $response = $this->get('/?lang=es');
        $response->assertSee('lang="es"', false);
        $this->flushSession();
    }

    /**
     * Test that the language cannot be manually changed to an invalid value.
     *
     * @return void
     */
    public function testSwitchLanguageInvalid()
    {
        $response = $this->get('/?lang=asdf');
        $response->assertSee('lang="en"', false);
        $this->flushSession();
    }

    /**
     * Test that the language can be changed via HTTP header
     *
     * @return void
     */
    public function testSwitchLanguageHeader()
    {
        $response = $this->get('/', [
            'Accept-Language' => 'ja',
        ]);
        $response->assertSee('lang="ja"', false);
        $this->flushSession();
    }

    /**
     * Test that the language setting persists in the session.
     *
     * @return void
     */
    public function testLanguageSessionPersistence()
    {
        $response = $this->get('/?lang=ja');
        $response->assertSee('lang="ja"', false);

        $response = $this->get('/videos');
        $response->assertSee('lang="ja"', false);
        $this->flushSession();
    }
}
