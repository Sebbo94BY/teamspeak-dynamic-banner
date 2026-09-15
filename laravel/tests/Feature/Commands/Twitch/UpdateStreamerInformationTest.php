<?php

namespace Tests\Feature\Commands\Twitch;

use App\Models\TwitchApi;
use App\Models\TwitchStreamer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
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

    /** Test a batch updates online, offline, and no-longer-existing Twitch accounts. */
    public function test_command_updates_all_streamer_states_from_batched_responses(): void
    {
        $online = $this->twitch_streamer;
        $online->stream_url = 'https://www.twitch.tv/online_streamer';
        $online->save();
        $offline = TwitchStreamer::factory()->create(['stream_url' => 'https://www.twitch.tv/offline_streamer', 'is_live' => true]);
        $missing = TwitchStreamer::factory()->create([
            'stream_url' => 'https://www.twitch.tv/missing_streamer',
            'is_live' => true,
            'game_name' => 'Old game',
            'title' => 'Old title',
            'viewer_count' => 12,
        ]);

        Http::fake(function (Request $request) {
            if (str_contains($request->url(), '/users?')) {
                return Http::response(['data' => [
                    ['id' => '1', 'login' => 'online_streamer'],
                    ['id' => '2', 'login' => 'offline_streamer'],
                ]]);
            }

            return Http::response(['data' => [[
                'user_id' => '1',
                'type' => 'live',
                'started_at' => '2026-09-16T10:00:00Z',
                'game_name' => 'New game',
                'title' => 'New title',
                'viewer_count' => 42,
            ]]]);
        });

        $this->artisan('twitch:update-streamer-information')->assertSuccessful();

        $this->assertDatabaseHas('twitch_streamers', ['id' => $online->id, 'is_live' => true, 'game_name' => 'New game', 'title' => 'New title', 'viewer_count' => 42]);
        $this->assertDatabaseHas('twitch_streamers', ['id' => $offline->id, 'is_live' => false]);
        $this->assertDatabaseHas('twitch_streamers', ['id' => $missing->id, 'is_live' => false, 'game_name' => null, 'title' => null, 'viewer_count' => 0]);
        Http::assertSentCount(2);
    }

    /** Test a failed batch does not prevent a following batch from updating. */
    public function test_command_continues_after_a_failed_batch(): void
    {
        $streamers = TwitchStreamer::factory()->count(101)->create();
        foreach ($streamers as $number => $streamer) {
            $streamer->stream_url = 'https://www.twitch.tv/streamer_'.($number + 1);
            $streamer->save();
        }

        Http::fake(function (Request $request) {
            if (str_contains($request->url(), '/users?') && str_contains($request->url(), 'streamer_101')) {
                return Http::response(['data' => [['id' => '101', 'login' => 'streamer_101']]]);
            }

            if (str_contains($request->url(), '/streams?') && str_contains($request->url(), 'user_id=101')) {
                return Http::response(['data' => [[
                    'user_id' => '101',
                    'type' => 'live',
                    'started_at' => '2026-09-16T10:00:00Z',
                    'game_name' => 'Game',
                    'title' => 'Title',
                    'viewer_count' => 1,
                ]]]);
            }

            return Http::response(['message' => 'temporary failure'], 503);
        });

        $this->artisan('twitch:update-streamer-information')->assertSuccessful();

        $this->assertDatabaseHas('twitch_streamers', ['id' => $streamers->last()->id, 'is_live' => true, 'viewer_count' => 1]);
        Http::assertSentCount(3);
    }
}
