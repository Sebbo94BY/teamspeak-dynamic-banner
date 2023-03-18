<?php

namespace Tests\Unit;

use App\Models\Banner;
use App\Models\BannerConfiguration;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_can_be_created()
    {
        $banner_template = BannerTemplate::factory()
            ->for(
                Banner::factory()->for(
                    Instance::factory()->create()
                )->create()
            )->for(
                Template::factory()->create()
            )->create();

        $banner_configuration = BannerConfiguration::factory()->for($banner_template)->create();

        $this->assertModelExists($banner_configuration);
    }

    public function test_model_relationships()
    {
        $banner_template = BannerTemplate::factory()
            ->for(
                Banner::factory()->for(
                    Instance::factory()->create()
                )->create()
            )->for(
                Template::factory()->create()
            )->create();

        $banner_configuration = BannerConfiguration::factory()->for($banner_template)->create();

        $this->assertInstanceOf(BannerTemplate::class, $banner_configuration->bannerTemplate);
    }
}
