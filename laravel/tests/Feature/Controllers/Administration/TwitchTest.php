<?php

namespace Tests\Feature\Controllers\Administration;

use App\Models\Localization;
use App\Models\TwitchApi;
use App\Models\TwitchStreamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwitchTest extends TestCase
{
    use RefreshDatabase;

    protected Localization $localization;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->localization = Localization::factory()->create();

        $this->user = User::factory()->for($this->localization)->create();
        $this->user->syncRoles('Twitch Super Admin');
    }

    /**
     * Test, that the user gets redirected to the login, when he is unauthenticated.
     */
    public function test_user_gets_redirected_to_login_when_unauthenticated(): void
    {
        $response = $this->get(route('administration.twitch'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test, that the user can access the page, when he is authenticated.
     */
    public function test_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('administration.twitch'));
        $response->assertStatus(200);
        $response->assertViewIs('administration.twitch');
    }

    /**
     * Test that upserting a the Twitch API credentials requires to match the request rules.
     */
    public function test_upserting_twitch_api_credentials_requires_to_match_the_request_rules(): void
    {
        $response = $this->actingAs($this->user)->patch(route('administration.twitch.update_api_credentials'), [
            'client_secret' => 'some_client_secret',
        ]);
        $response->assertSessionHasErrors(['client_id']);

        $response = $this->actingAs($this->user)->patch(route('administration.twitch.update_api_credentials'), [
            'client_id' => 'some_client_id',
        ]);
        $response->assertSessionHasErrors(['client_secret']);

        $response = $this->actingAs($this->user)->patch(route('administration.twitch.update_api_credentials'), [
            'client_id' => 'some_client_id',
            'client_secret' => 'some_client_secret',
        ]);
        $response->assertSessionHasNoErrors();
    }

    /**
     * Test that upserting the Twitch API credentials with invalid credentials returns the view with an error.
     */
    public function test_upserting_the_twitch_api_credentials_with_invalid_credentials_returns_the_view_with_an_error(): void
    {
        $response = $this->actingAs($this->user)->patch(route('administration.twitch.update_api_credentials'), [
            'client_id' => 'some_invalid_client_id',
            'client_secret' => 'some_invalid_client_secret',
        ]);
        $response->assertRedirectToRoute('administration.twitch');
        $response->assertSessionHas('error');
    }

    /**
     * Test that deleting the Twitch API credentials returns the view with a success message.
     */
    public function test_deleting_the_twitch_api_credentials_returns_the_view_with_a_success_message(): void
    {
        TwitchApi::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('administration.twitch.delete_api_credentials'), []);
        $response->assertRedirectToRoute('administration.twitch');
        $response->assertSessionHas('success');
    }

    /**
     * Test that adding a new Twitch streamer requires to match the request rules.
     */
    public function test_adding_a_new_twitch_streamer_requires_to_match_the_request_rules(): void
    {
        $response = $this->actingAs($this->user)->post(route('administration.twitch.create_streamer'), [
            'stream_url' => 'https://www.github.com/some/url',
        ]);
        $response->assertSessionHasErrors(['stream_url']);

        $response = $this->actingAs($this->user)->post(route('administration.twitch.create_streamer'), [
            'stream_url' => 'https://www.twitch.tv/randomStream',
        ]);
        $response->assertSessionHasNoErrors();
    }

    /**
     * Test that adding a new Twitch streamer successfully returns the view.
     */
    public function test_adding_a_new_twitch_streamer_successfully_returns_the_view(): void
    {
        $response = $this->actingAs($this->user)->post(route('administration.twitch.create_streamer'), [
            'stream_url' => 'https://www.twitch.tv/randomStream',
        ]);
        $response->assertRedirectToRoute('administration.twitch');
        $response->assertSessionHas('success');
    }

    /**
     * Test that updating an existing user requires to match the request rules.
     */
    public function test_updating_an_existing_twitch_streamer_requires_to_match_the_request_rules(): void
    {
        $other_twitch_streamer = TwitchStreamer::factory()->create();

        $response = $this->actingAs($this->user)->patch(route('administration.twitch.update_streamer', ['twitch_streamer_id' => $other_twitch_streamer->id]), [
            'stream_url' => 'https://www.github.com/some/url',
        ]);
        $response->assertSessionHasErrors(['stream_url']);

        $response = $this->actingAs($this->user)->patch(route('administration.twitch.update_streamer', ['twitch_streamer_id' => $other_twitch_streamer->id]), [
            'stream_url' => 'https://www.twitch.tv/randomStream',
        ]);
        $response->assertSessionHasNoErrors();
    }

    /**
     * Test that updating an existing Twitch streamer is working as expected.
     */
    public function test_updating_an_existing_twitch_streamer_is_working_as_expected(): void
    {
        $other_twitch_streamer = TwitchStreamer::factory()->create();

        $response = $this->actingAs($this->user)->patch(route('administration.twitch.update_streamer', ['twitch_streamer_id' => $other_twitch_streamer->id]), [
            'stream_url' => 'https://www.twitch.tv/randomStream',
        ]);
        $response->assertRedirectToRoute('administration.twitch');
        $response->assertSessionHas('success');
    }

    /**
     * Test that trying to delete a Twitch streamer ID, which exists, returns the respective view.
     */
    public function test_delete_twitch_streamer_returns_the_view_when_the_given_id_exists(): void
    {
        $other_twitch_streamer = TwitchStreamer::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('administration.twitch.delete_streamer', ['twitch_streamer_id' => $other_twitch_streamer->id]));
        $response->assertRedirectToRoute('administration.twitch');
        $response->assertSessionHas('success');
    }
}
