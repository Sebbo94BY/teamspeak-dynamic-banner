<?php

namespace Tests\Feature\Commands\Twitch;

use App\Models\TwitchApi;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateApiAccessTokenTest extends TestCase
{
    use RefreshDatabase;

    protected TwitchApi $twitch_api;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->twitch_api = TwitchApi::factory()->create();
    }

    /**
     * Test that the command immediately aborts, when no API credentials have been configured.
     */
    public function test_command_immediately_aborts_when_no_api_credentials_have_been_configured(): void
    {
        $this->twitch_api->delete();

        $this->artisan('twitch:update-api-access-token')
            ->expectsOutput('No twitch API has been configured yet. Skipping.')
            ->doesntExpectOutput('Performing a Twitch API authentication...')
            ->assertSuccessful();
    }

    /**
     * Test that the command immediately aborts, when the existing access token is at least valid for 30 minutes.
     */
    public function test_command_immediately_aborts_when_the_existing_access_token_is_at_least_valid_for_30_minutes(): void
    {
        $this->twitch_api->expires_at = Carbon::now()->addHour();
        $this->twitch_api->save();

        $this->artisan('twitch:update-api-access-token')
            ->expectsOutput('The existing access token is still valid for at least the next 30 minutes. Skipping.')
            ->doesntExpectOutput('Performing a Twitch API authentication...')
            ->assertSuccessful();
    }

    /**
     * Test that the command aborts, when invalid API credentials have been provided.
     */
    public function test_command_aborts_when_invalid_api_credentials_have_been_provided(): void
    {
        $this->twitch_api->access_token = 'invalid_access_token';
        $this->twitch_api->save();

        $this->artisan('twitch:update-api-access-token')
            ->expectsOutput('Performing a Twitch API authentication...')
            ->expectsOutputToContain('The Twitch API authentication request failed due to the following error:')
            ->assertSuccessful();
    }
}
