<?php

namespace Tests\Unit;

use App\Models\Banner;
use App\Models\BannerConfiguration;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_can_be_created()
    {
        $banner_template = BannerTemplate::factory()->for(
            Banner::factory()->for(
                Instance::factory()->create()
            )->create()
        )->for(
            Template::factory()->create()
        )->create();

        $this->assertModelExists($banner_template);
    }

    public function test_model_relationships()
    {
        $banner_template = BannerTemplate::factory()->for(
            Banner::factory()->for(
                Instance::factory()->create()
            )->create()
        )->for(
            Template::factory()->create()
        )->create();

        BannerConfiguration::factory(3)->for($banner_template)->create();

        $this->assertInstanceOf(Banner::class, $banner_template->banner);
        $this->assertInstanceOf(Template::class, $banner_template->template);
        $this->assertContainsOnlyInstancesOf(BannerConfiguration::class, $banner_template->configurations);
        $this->assertEquals(3, $banner_template->configurations->count());
    }
}
