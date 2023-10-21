<?php

namespace Tests\Feature\Commands\Banners;

use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnableBannerTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected BannerTemplate $banner_template;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

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
     * Test that the banner template does not get enabled when no `enable_at` timestamp has been defined.
     */
    public function test_banner_template_does_not_get_enabled_when_unconfigured(): void
    {
        $this->artisan('banners:enable-templates')
            ->expectsOutput('Checking 0 banner templates...')
            ->assertSuccessful();
    }

    /**
     * Test that the banner template does not get enabled when the `enable_at` timestamp has been defined, but is in the future.
     */
    public function test_banner_template_does_not_get_enabled_when_configured_but_in_the_future(): void
    {
        $this->banner_template->enable_at = Carbon::now()->addMinutes(5);
        $this->banner_template->save();

        $this->artisan('banners:enable-templates')
            ->expectsOutput('The configured date and time for the banner template with the ID '.$this->banner_template->id.' is still in the future. Skipping.')
            ->assertSuccessful();
    }

    /**
     * Test that the banner template gets enabled when the `enable_at` timestamp has been defined and is in the past.
     */
    public function test_banner_template_gets_enabled_when_configured_and_in_the_past(): void
    {
        $this->banner_template->enable_at = Carbon::now()->subMinutes(5);
        $this->banner_template->save();

        $this->artisan('banners:enable-templates')
            ->expectsOutput('Successfully enabled the banner template with the ID '.$this->banner_template->id.'. See '.route('banner.template.configuration.edit', ['banner_template_id' => $this->banner_template->id]).' for details.')
            ->assertSuccessful();
    }
}
