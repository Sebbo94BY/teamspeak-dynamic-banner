<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\BannerConfiguration;
use App\Models\BannerTemplate;
use App\Models\Font;
use App\Models\Instance;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerConfigurationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected BannerTemplate $banner_template;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->user = User::factory()->create();
        $this->user->syncRoles('Banners Admin');

        $this->banner_template = BannerTemplate::factory()
            ->for(
                Banner::factory()->for(
                    Instance::factory()->create()
                )->create()
            )->for(
                Template::factory()->create()
            )->create();
    }

    /**
     * Test, that the user gets redirected to the login, when he is unauthenticated.
     */
    public function test_user_gets_redirected_to_login_when_unauthenticated(): void
    {
        $response = $this->get(route('banner.template.configuration.edit', ['banner_template_id' => $this->banner_template->id]));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test, that the user can access the page, when he is authenticated.
     */
    public function test_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('banner.template.configuration.edit', ['banner_template_id' => $this->banner_template->id]));
        $response->assertStatus(200);
        $response->assertViewIs('banner.configuration');
        $response->assertViewHas('banner_template');
        $response->assertViewHas('fonts');
    }

    /**
     * Test that upserting a banner configuration requires to match the request rules.
     */
    public function test_upserting_a_banner_configuration_requires_to_match_the_request_rules(): void
    {
        $response = $this->actingAs($this->user)->patch(route('banner.template.configuration.upsert', ['banner_template_id' => $this->banner_template->id]), [
            'banner_template_id' => $this->banner_template->id,
            'configuration' => [
                'y_coordinate' => [$this->banner_template->template->height],
                'text' => [fake()->text(32)],
                'font_id' => [Font::factory()->create()->id],
                'font_size' => [fake()->numberBetween(1, $this->banner_template->template->height)],
                'font_angle' => [fake()->numberBetween(0, 360)],
                'font_color_in_hexadecimal' => [fake()->hexColor()],
            ],
        ]);
        $response->assertSessionHasErrors(['configuration.x_coordinate']);

        $response = $this->actingAs($this->user)->patch(route('banner.template.configuration.upsert', ['banner_template_id' => $this->banner_template->id]), [
            'banner_template_id' => $this->banner_template->id,
            'configuration' => [
                'x_coordinate' => [9999],
                'y_coordinate' => [$this->banner_template->template->height],
                'text' => [fake()->text(32)],
                'font_id' => [Font::factory()->create()->id],
                'font_size' => [fake()->numberBetween(1, $this->banner_template->template->height)],
                'font_angle' => [fake()->numberBetween(0, 360)],
                'font_color_in_hexadecimal' => [fake()->hexColor()],
            ],
        ]);
        $response->assertSessionHasErrors(['configuration.x_coordinate.0']);
    }

    /**
     * Test that upserting a banner configuration returns an error when the template could not be drawn.
     */
    public function test_upserting_a_banner_configuration_returns_an_error_when_the_template_could_not_be_drawn(): void
    {
        $response = $this->actingAs($this->user)->patch(route('banner.template.configuration.upsert', ['banner_template_id' => $this->banner_template->id]), [
            'banner_template_id' => $this->banner_template->id,
            'configuration' => [
                'x_coordinate' => [$this->banner_template->template->width],
                'y_coordinate' => [$this->banner_template->template->height],
                'text' => [fake()->text(32)],
                'font_id' => [Font::factory()->create()->id],
                'font_size' => [fake()->numberBetween(1, $this->banner_template->template->height)],
                'font_angle' => [fake()->numberBetween(0, 360)],
                'font_color_in_hexadecimal' => [fake()->hexColor()],
            ],
        ]);

        $response->assertRedirectToRoute('banner.template.configuration.edit', ['banner_template_id' => $this->banner_template->id]);
        $response->assertSessionHas('banner_template');
        $response->assertSessionHas('error');
    }

    /**
     * Test that deleting a banner configuration returns an error when the updated template could not be drawn.
     */
    public function test_deleting_a_banner_configuration_returns_an_error_when_the_updated_template_could_not_be_drawn(): void
    {
        $banner_configuration = BannerConfiguration::factory()
            ->for($this->banner_template)
            ->for(Font::factory()->create())
            ->create();

        $response = $this->actingAs($this->user)->get(route('banner.template.configuration.delete', ['banner_configuration_id' => $banner_configuration->id]));
        $response->assertRedirectToRoute('banner.template.configuration.edit', ['banner_template_id' => $this->banner_template->id]);
        $response->assertSessionHas('error');
    }
}
