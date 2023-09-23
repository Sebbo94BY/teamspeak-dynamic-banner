<?php

namespace Tests\Feature;

use App\Jobs\DrawGridSystemOnTemplate;
use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Banner $banner;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->user = User::factory()->create();
        $this->user->syncRoles('Banners Admin');

        $this->banner = Banner::factory()->for(
            Instance::factory()->create()
        )->create();
    }

    /**
     * Test, that the user gets redirected to the login, when he is unauthenticated.
     */
    public function test_user_gets_redirected_to_login_when_unauthenticated(): void
    {
        $response = $this->get(route('banner.templates', ['banner_id' => $this->banner->id]));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test, that the user can access the page, when he is authenticated.
     */
    public function test_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('banner.templates', ['banner_id' => $this->banner->id]));
        $response->assertStatus(200);
        $response->assertViewIs('banner.template');
        $response->assertViewHas('banner');
        $response->assertViewHas('templates');
    }

    /**
     * Test that adding a new template to a banner requires to match the request rules.
     */
    public function test_adding_a_new_template_to_a_banner_requires_to_match_the_request_rules(): void
    {
        $response = $this->actingAs($this->user)->post(route('banner.add.template'), [
            'banner_id' => $this->banner->id,
        ]);
        $response->assertSessionHasErrors(['template_id']);
    }

    /**
     * Test, that the user can access the "edit banner" page, when the requested banner ID for the edit page exists.
     */
    public function test_edit_banner_page_gets_displayed_when_banner_id_exists(): void
    {
        $response = $this->actingAs($this->user)->get(route('banner.templates', ['banner_id' => $this->banner->id]));
        $response->assertViewIs('banner.template');
        $response->assertViewHas('banner');
        $response->assertViewHas('templates');
    }

    /**
     * Test that a template can be added to a banner.
     */
    public function test_a_template_can_be_added_to_a_banner(): void
    {
        $template = Template::factory()->create();

        // Generate a temporary image to be able to test the API
        $absolut_upload_directory = public_path($template->file_path_original);
        $image_file_path = $absolut_upload_directory.'/'.$template->filename;
        $gd_image = imagecreate(1024, 300);
        imagecolorallocate($gd_image, 0, 0, 0);
        if (! file_exists($absolut_upload_directory)) {
            mkdir($absolut_upload_directory, 0777, true);
        }
        imagepng($gd_image, $image_file_path);
        DrawGridSystemOnTemplate::dispatchSync($template);

        $response = $this->actingAs($this->user)->post(route('banner.add.template'), [
            'name' => fake()->name(),
            'banner_id' => $this->banner->id,
            'template_id' => $template->id,
        ]);

        $response->assertRedirectToRoute('banner.template.configuration.edit', ['banner_template_id' => BannerTemplate::latest('created_at')->first()->id]);
        $response->assertSessionHas('success');
        $this->assertEquals(1, count($this->banner->templates));
    }

    /**
     * Test that a template can be removed from a banner.
     */
    public function test_a_template_can_be_removed_from_a_banner(): void
    {
        $banner_template = BannerTemplate::factory()
            ->for(
                $this->banner
            )
            ->for(
                Template::factory()->create()
            )->create();

        $response = $this->actingAs($this->user)->delete(route('banner.template.remove', ['banner_template_id' => $banner_template->id]));

        $response->assertRedirectToRoute('banner.templates', ['banner_id' => $this->banner->id]);
        $response->assertSessionHas('success');
        $this->assertEquals(0, $this->banner->templates->count());
    }

    /**
     * Test that a template of a banner can be disabled.
     */
    public function test_a_template_of_a_banner_can_be_disabled(): void
    {
        $banner_template = BannerTemplate::factory()
            ->for(
                $this->banner
            )
            ->for(
                Template::factory()->create()
            )->create(['enabled' => true]);

        $this->assertTrue(BannerTemplate::find($banner_template->id)->enabled);
        $response = $this->actingAs($this->user)->patch(route('banner.template.disable', ['banner_template_id' => $banner_template->id]));

        $response->assertRedirectToRoute('banner.templates', ['banner_id' => $this->banner->id]);
        $response->assertSessionHas('success');
        $this->assertFalse(BannerTemplate::find($banner_template->id)->enabled);
    }

    /**
     * Test that a template of a banner can be enabled.
     */
    public function test_a_template_of_a_banner_can_be_enabled(): void
    {
        $banner_template = BannerTemplate::factory()
            ->for(
                $this->banner
            )
            ->for(
                Template::factory()->create()
            )->create(['enabled' => false]);

        $this->assertFalse(BannerTemplate::find($banner_template->id)->enabled);
        $response = $this->actingAs($this->user)->patch(route('banner.template.enable', ['banner_template_id' => $banner_template->id]));

        $response->assertRedirectToRoute('banner.templates', ['banner_id' => $this->banner->id]);
        $response->assertSessionHas('success');
        $this->assertTrue(BannerTemplate::find($banner_template->id)->enabled);
    }
}
