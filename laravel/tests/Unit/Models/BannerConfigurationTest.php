<?php

namespace Tests\Unit\Models;

use App\Models\Banner;
use App\Models\BannerConfiguration;
use App\Models\BannerTemplate;
use App\Models\Font;
use App\Models\Instance;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerConfigurationTest extends TestCase
{
    use RefreshDatabase;

    protected BannerConfiguration $banner_configuration;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->banner_configuration = BannerConfiguration::factory()
            ->for(
                BannerTemplate::factory()
                    ->for(
                        Banner::factory()->for(
                            Instance::factory()->create()
                        )->create()
                    )
                    ->for(Template::factory()->create())
                    ->create()
            )
            ->for(
                Font::factory()->create()
            )
            ->create();
    }

    /**
     * Test that the model can be created.
     */
    public function test_model_can_be_created(): void
    {
        $this->assertModelExists($this->banner_configuration);
    }

    /**
     * Test that the model has one linked banner template.
     */
    public function test_model_has_one_linked_banner_template(): void
    {
        $this->assertInstanceOf(BannerTemplate::class, $this->banner_configuration->bannerTemplate);
    }
}
