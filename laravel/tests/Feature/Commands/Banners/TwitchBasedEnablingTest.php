<?php

namespace Tests\Feature\Commands\Banners;

use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Template;
use App\Models\TwitchApi;
use App\Models\TwitchStreamer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwitchBasedEnablingTest extends TestCase
{
    use RefreshDatabase;

    protected TwitchStreamer $twitch_streamer;

    protected BannerTemplate $banner_template;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        TwitchApi::factory()->create();

        $this->twitch_streamer = TwitchStreamer::factory()->create();

        $this->banner_template = BannerTemplate::factory()
            ->for(
                Banner::factory()->for(
                    Instance::factory()->create()
                )->create()
            )
            ->for(Template::factory()->create())
            ->create(['enabled' => false]);
    }

    /**
     * Test that the command immediately exits when no Twitch API credentials are configured.
     */
    public function test_command_immediately_exits_when_no_twitch_api_credentials_are_configured(): void
    {
        TwitchApi::truncate();

        $this->artisan('banners:twitch-based-enabling')
            ->expectsOutput('No twitch API has been configured yet. Skipping.')
            ->doesntExpectOutputToContain('Checking')
            ->assertSuccessful();
    }

    /**
     * Test that the banner template does not get enabled when no `twitch_streamer_id` has been defined.
     */
    public function test_banner_template_does_not_get_enabled_when_unconfigured(): void
    {
        $this->artisan('banners:twitch-based-enabling')
            ->expectsOutput('Checking 0 banner templates...')
            ->assertSuccessful();
    }

    /**
     * Test that the banner template does not get enabled when the `twitch_streamer_id` has been defined, but the streamer is offline.
     */
    public function test_banner_template_does_not_get_enabled_when_configured_but_the_streamer_is_offline(): void
    {
        $this->banner_template->twitch_streamer_id = $this->twitch_streamer->id;
        $this->banner_template->save();

        $this->twitch_streamer->is_live = false;
        $this->twitch_streamer->save();

        $this->artisan('banners:twitch-based-enabling')
            ->expectsOutput('The banner template with the ID '.$this->banner_template->id.' should NOT be enabled yet. Skipping.')
            ->assertSuccessful();
    }

    /**
     * Test that the command continues, when the respective banner template is already enabled.
     */
    public function test_command_continues_when_the_banner_template_is_already_enabled(): void
    {
        $this->banner_template->twitch_streamer_id = $this->twitch_streamer->id;
        $this->banner_template->enabled = true;
        $this->banner_template->save();

        $this->artisan('banners:twitch-based-enabling')
            ->expectsOutput('The banner template with the ID '.$this->banner_template->id.' is already enabled. Skipping.')
            ->doesntExpectOutput('Successfully enabled the banner template with the ID '.$this->banner_template->id.'. See '.route('banner.template.configuration.edit', ['banner_template_id' => $this->banner_template->id]).' for details.')
            ->assertSuccessful();
    }

    /**
     * Test that the banner template gets enabled when the `twitch_streamer_id` has been defined and the streamer is online.
     */
    public function test_banner_template_gets_enabled_when_configured_and_the_streamer_is_online(): void
    {
        $this->banner_template->twitch_streamer_id = $this->twitch_streamer->id;
        $this->banner_template->save();

        $this->twitch_streamer->is_live = true;
        $this->twitch_streamer->save();

        $this->artisan('banners:twitch-based-enabling')
            ->expectsOutput('Successfully enabled the banner template with the ID '.$this->banner_template->id.'. See '.route('banner.template.configuration.edit', ['banner_template_id' => $this->banner_template->id]).' for details.')
            ->assertSuccessful();
    }
}
