<?php

namespace Tests\Feature\API\v1;

use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use RefreshDatabase;

    protected Banner $banner;

    public function setUp(): void
    {
        parent::setUp();

        $this->banner = Banner::factory()->for(
            Instance::factory()->create()
        )->create();
    }

    /**
     * Ensure, that the API endpoint requires a banner ID as parameter.
     */
    public function test_api_requires_banner_id_parameter(): void
    {
        $this->expectExceptionMessageMatches('/Missing required parameter/i');
        $response = $this->get(route('api.banner'));
        $response->assertStatus(404);
    }

    /**
     * Ensure, that the API endpoint requires an existing banner ID as parameter.
     */
    public function test_api_requires_existing_banner_id_parameter(): void
    {
        $response = $this->get(route('api.banner', ['banner_id' => 'abc']));
        $response->assertSeeText('Invalid Banner ID in the URL.');
        $response->assertStatus(404);

        $response = $this->get(route('api.banner', ['banner_id' => 1337]));
        $response->assertSeeText('Invalid Banner ID in the URL.');
        $response->assertStatus(404);
    }

    /**
     * Ensure, that the API endpoint returns an error, when it has no linked templates at all.
     */
    public function test_api_returns_error_when_no_templates_are_linked(): void
    {
        $response = $this->get(route('api.banner', ['banner_id' => base_convert($this->banner->id, 10, 35)]));
        $response->assertSeeText('The banner does either not have any configured templates or all of them are disabled.');
        $response->assertStatus(401);
    }

    /**
     * Ensure, that the API endpoint returns an error, when the template has no configuration at all.
     */
    public function test_api_returns_error_when_the_template_has_no_configuration(): void
    {
        $banner_template = BannerTemplate::factory()
            ->for(
                $this->banner
            )->for(
                Template::factory()->create()
            )->create();
        $banner_template->enabled = true;
        $banner_template->save();

        $response = $this->get(route('api.banner', ['banner_id' => base_convert($this->banner->id, 10, 35)]));
        $response->assertSeeText('The template does not have any configurations. This seems wrong.');
        $response->assertStatus(500);
    }
}
