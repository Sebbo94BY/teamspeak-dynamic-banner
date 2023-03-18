<?php

namespace Tests\Unit;

use App\Models\Banner;
use App\Models\Instance;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_can_be_created()
    {
        $banner = Banner::factory()->for(
            Instance::factory()->create()
        )->create();

        $this->assertModelExists($banner);
        $this->assertIsBool($banner->random_rotation);
    }

    public function test_model_casts()
    {
        $banner = Banner::factory()->for(
            Instance::factory()->create()
        )->create();

        $this->assertIsBool($banner->random_rotation);
    }

    public function test_model_relationships()
    {
        $banner = Banner::factory()->for(
            Instance::factory()->create()
        )->create();

        $this->assertInstanceOf(Instance::class, $banner->instance);
        $this->assertContainsOnlyInstancesOf(Template::class, $banner->templates);
    }
}
