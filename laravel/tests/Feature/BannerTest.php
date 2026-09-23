<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Localization;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Instance $instance;

    protected Banner $banner;

    protected function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->user = User::factory()->for(Localization::factory()->create())->create();
        $this->user->syncRoles('Banners Admin');

        $this->instance = Instance::factory()->create();

        $this->banner = Banner::factory()->for(
            $this->instance
        )->create();
    }

    /**
     * Test, that the user gets redirected to the login, when he is unauthenticated.
     */
    public function test_user_gets_redirected_to_login_when_unauthenticated(): void
    {
        $response = $this->get(route('banners'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test, that the user can access the page, when he is authenticated.
     */
    public function test_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('banners'));
        $response->assertStatus(200);
        $response->assertViewIs('banners');
        $response->assertViewHas('banners');
    }

    /**
     * Test that attention filters list only the banners matching the selected issue.
     */
    public function test_attention_filters_only_show_banners_needing_attention(): void
    {
        $bannerWithActiveTemplate = Banner::factory()->for($this->instance)->create();
        BannerTemplate::factory()
            ->for($bannerWithActiveTemplate)
            ->for(Template::factory()->create())
            ->create(['enabled' => true]);
        $bannerWithoutActiveTemplate = Banner::factory()->for($this->instance)->create();
        BannerTemplate::factory()
            ->for($bannerWithoutActiveTemplate)
            ->for(Template::factory()->create())
            ->create(['enabled' => false]);

        $withoutTemplates = $this->actingAs($this->user)->get(route('banners', ['attention' => 'without-templates']));
        $withoutTemplates->assertOk();
        $withoutTemplates->assertViewHas('banners', fn ($banners) => $banners->contains('id', $this->banner->id)
            && ! $banners->contains('id', $bannerWithActiveTemplate->id)
            && ! $banners->contains('id', $bannerWithoutActiveTemplate->id));

        $withoutActiveTemplates = $this->actingAs($this->user)->get(route('banners', ['attention' => 'without-active-templates']));
        $withoutActiveTemplates->assertOk();
        $withoutActiveTemplates->assertViewHas('banners', fn ($banners) => $banners->contains('id', $bannerWithoutActiveTemplate->id)
            && ! $banners->contains('id', $this->banner->id)
            && ! $banners->contains('id', $bannerWithActiveTemplate->id));
    }

    /**
     * Test that adding a new banner requires to match the request rules.
     */
    public function test_adding_a_new_banner_requires_to_match_the_request_rules(): void
    {
        $response = $this->actingAs($this->user)->post(route('banner.save'), [
            'name' => fake()->name(),
        ]);
        $response->assertSessionHasErrors(['instance_id']);
    }

    /**
     * Test that a new banner can be added.
     */
    public function test_adding_a_new_banner_is_possible(): void
    {
        $instance = Instance::factory()->create();

        $response = $this->actingAs($this->user)->post(route('banner.save'), [
            'name' => fake()->name(),
            'instance_id' => $instance->id,
        ]);
        $response->assertRedirectToRoute('banners');
        $response->assertSessionHas('success');
    }

    /**
     * Test that updating an existing banner requires to match the request rules.
     */
    public function test_updating_an_existing_banner_requires_to_match_the_request_rules(): void
    {
        $response = $this->actingAs($this->user)->patch(route('banner.update', ['banner_id' => $this->banner->id]), [
            'name' => fake()->name(),
        ]);
        $response->assertSessionHasErrors(['instance_id']);
    }

    /**
     * Test that updating an existing banner is possible.
     */
    public function test_updating_an_existing_banner_is_possible(): void
    {
        $this->assertEquals($this->instance->id, Banner::find($this->banner->id)->instance->id);

        $instance = Instance::factory()->create();

        $response = $this->actingAs($this->user)->patch(route('banner.update', ['banner_id' => $this->banner->id]), [
            'name' => fake()->name(),
            'instance_id' => $instance->id,
        ]);
        $response->assertRedirectToRoute('banners');
        $response->assertSessionHas('success');
        $this->assertEquals($instance->id, Banner::find($this->banner->id)->instance->id);
    }

    /**
     * Test that trying to delete a banner ID, which exists, returns the respective view.
     */
    public function test_deleting_an_existing_banner_is_possible(): void
    {
        $response = $this->actingAs($this->user)->delete(route('banner.delete', ['banner_id' => $this->banner->id]));
        $response->assertRedirectToRoute('banners');
        $response->assertSessionHas('success');
    }
}
