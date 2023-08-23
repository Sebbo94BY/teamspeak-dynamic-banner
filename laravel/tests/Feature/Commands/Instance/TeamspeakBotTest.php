<?php

namespace Tests\Feature\Commands\Instance;

use App\Models\Instance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamspeakBotTest extends TestCase
{
    use RefreshDatabase;

    protected Instance $instance;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->instance = Instance::factory()->create();
    }

    /**
     * Test that the teamspeak bot can be started.
     */
    public function test_teamspeak_bot_can_be_started(): void
    {
        $this->artisan('instance:start-teamspeak-bot', ['instance_id' => $this->instance->id])
            ->expectsOutputToContain('My Process ID (PID) is')
            ->expectsOutput('Setting up signal handlers.')
            ->expectsOutput('Starting TeamSpeak bot instance: '.$this->instance->virtualserver_name)
            ->assertSuccessful();
    }
}
