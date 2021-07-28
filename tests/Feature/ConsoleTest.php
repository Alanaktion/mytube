<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConsoleTest extends TestCase
{
    use WithFaker;

    /**
     * Test adding a user from the console.
     */
    public function testAddUser(): void
    {
        $this->artisan('user:add')
            ->expectsQuestion("User's name", $this->faker->name)
            ->expectsQuestion("User's email", $this->faker->email)
            ->expectsQuestion("Choose a password", $this->faker->password)
            ->assertExitCode(0);
    }

    /**
     * Test listing the import sources.
     */
    public function testInputSources(): void
    {
        /**
         * @var \App\Sources\Source[] $sources
         */
        $sources = app()->tagged('sources');
        $command = $this->artisan('import:sources');
        foreach ($sources as $source) {
            $command->expectsOutput($source->getSourceType());
        }
        $command->assertExitCode(0);
    }
}
