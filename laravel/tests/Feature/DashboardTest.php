<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\InstanceProcess;
use App\Models\Localization;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->for(Localization::factory()->create())->create();
    }

    /**
     * Test, that the user gets redirected to the login, when he is unauthenticated.
     */
    public function test_user_gets_redirected_to_login_when_unauthenticated(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test, that the user can access the page, when he is authenticated.
     */
    public function test_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    /**
     * Test that operational issues and recent dashboard data are exposed to the view.
     */
    public function test_dashboard_provides_an_operational_overview(): void
    {
        $running_instance = Instance::factory()->create();
        InstanceProcess::factory()->for($running_instance)->create();
        $stopped_instance = Instance::factory()->create();

        $banner_without_template = Banner::factory()->for($running_instance)->create();
        $banner_without_active_template = Banner::factory()->for($running_instance)->create();
        $bannerTemplate = BannerTemplate::factory()
            ->for($banner_without_active_template)
            ->for(Template::factory()->create())
            ->create(['enabled' => false]);
        $unused_template = Template::factory()->create();

        $this->actingAs($this->user);
        $bannerTemplate->name = 'Recently changed banner template';
        $bannerTemplate->updated_at = now()->addMinute();
        $bannerTemplate->save();

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('running_instances_count', 1);
        $response->assertViewHas('stopped_instances', fn ($instances) => $instances->contains('id', $stopped_instance->id));
        $response->assertViewHas('banners_without_templates', fn ($banners) => $banners->contains('id', $banner_without_template->id));
        $response->assertViewHas('banners_without_active_templates', fn ($banners) => $banners->contains('id', $banner_without_active_template->id));
        $response->assertViewHas('unused_templates', fn ($templates) => $templates->contains('id', $unused_template->id));
        $response->assertViewHas('recent_changes');
        $response->assertViewHas('recent_banner_renders');
        $response->assertViewHas('recent_changes', fn ($changes) => $changes->contains(
            fn ($change) => $change->name === $banner_without_active_template->name && $change->changed_by === $this->user->name,
        ));
    }

    /**
     * Test that a clean installation does not report configuration issues.
     */
    public function test_dashboard_does_not_report_issues_when_no_configuration_exists(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('stopped_instances', fn ($instances) => $instances->isEmpty());
        $response->assertViewHas('banners_without_templates', fn ($banners) => $banners->isEmpty());
        $response->assertViewHas('banners_without_active_templates', fn ($banners) => $banners->isEmpty());
        $response->assertViewHas('unused_templates', fn ($templates) => $templates->isEmpty());
        $response->assertViewHas('static_templates_count', 0);
        $response->assertViewHas('animated_templates_count', 0);
    }

    /**
     * Test that static and animated template files are counted separately.
     */
    public function test_dashboard_groups_templates_by_animation_type(): void
    {
        Template::factory()->create(['filename' => 'status-banner.png']);
        Template::factory()->create(['filename' => 'animated-status-banner.gif']);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('static_templates_count', 1);
        $response->assertViewHas('animated_templates_count', 1);
    }

    /**
     * Test that successful banner renderings are shown before banners never rendered.
     */
    public function test_dashboard_lists_recent_successful_banner_renderings(): void
    {
        $instance = Instance::factory()->create();
        $renderedBanner = Banner::factory()->for($instance)->create();
        BannerTemplate::factory()
            ->for($renderedBanner)
            ->for(Template::factory()->create())
            ->create(['last_rendered_at' => now()->subMinute()]);
        $notRenderedBanner = Banner::factory()->for($instance)->create();
        BannerTemplate::factory()
            ->for($notRenderedBanner)
            ->for(Template::factory()->create())
            ->create(['last_rendered_at' => null]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('recent_banner_renders', function ($banners) use ($renderedBanner, $notRenderedBanner) {
            return $banners->first()->is($renderedBanner)
                && $banners->contains('id', $notRenderedBanner->id);
        });
    }
}
