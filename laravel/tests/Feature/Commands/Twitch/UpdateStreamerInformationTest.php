<?php

namespace Tests\Feature\Commands\Twitch;

use App\Models\TwitchApi;
use App\Models\TwitchStreamer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateStreamerInformationTest extends TestCase
{
    use RefreshDatabase;

    protected TwitchApi $twitch_api;

    protected TwitchStreamer $twitch_streamer;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->twitch_api = TwitchApi::factory()->create();

        $this->twitch_streamer = TwitchStreamer::factory()->create();
    }

    /**
     * Test that the command immediately aborts, when no API credentials have been configured.
     */
    public function test_command_immediately_aborts_when_no_api_credentials_have_been_configured(): void
    {
        $this->twitch_api->delete();

        $this->artisan('twitch:update-streamer-information')
            ->expectsOutput('No Twitch API credentials have been provided yet. Doing nothing.')
            ->doesntExpectOutputToContain('Retrieving current Twitch stream information for')
            ->assertSuccessful();
    }

    /**
     * Test that the command aborts, when invalid API credentials have been provided.
     */
    public function test_command_aborts_when_invalid_api_credentials_have_been_provided(): void
    {
        $this->twitch_api->access_token = 'invalid_access_token';
        $this->twitch_api->save();

        $this->artisan('twitch:update-streamer-information')
            ->expectsOutput('Retrieving current Twitch stream information for 1 streams...')
            ->expectsOutputToContain('The Twitch API request failed due to the following error:')
            ->assertSuccessful();
    }
}
