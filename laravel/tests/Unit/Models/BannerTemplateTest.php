<?php

namespace Tests\Unit\Models;

use App\Models\Banner;
use App\Models\BannerConfiguration;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected Banner $banner;

    protected Template $template;

    protected BannerTemplate $banner_template;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->banner = Banner::factory()->for(
            Instance::factory()->create()
        )->create();

        $this->template = Template::factory()->create();

        $this->banner_template = BannerTemplate::factory()
            ->for($this->banner)
            ->for($this->template)
            ->create();
    }

    /**
     * Test that the model can be created.
     */
    public function test_model_can_be_created(): void
    {
        $this->assertModelExists($this->banner_template);
    }

    /**
     * Test that the model properly casts specific attributes.
     */
    public function test_model_properly_casts_specific_attributes(): void
    {
        $this->assertNull($this->banner_template->disable_at);

        $this->banner_template->disable_at = '2023-08-23 17:46';
        $this->banner->save();
        $this->assertInstanceOf(Carbon::class, $this->banner_template->disable_at);
        $this->assertEquals('2023-08-23 17:46', Carbon::parse($this->banner_template->disable_at)->format('Y-m-d H:i'));

        $this->assertIsBool($this->banner_template->enabled);
    }

    /**
     * Test that the model has one linked banner.
     */
    public function test_model_has_one_linked_banner(): void
    {
        $this->assertInstanceOf(Banner::class, $this->banner_template->banner);
    }

    /**
     * Test that the model has one linked template.
     */
    public function test_model_has_one_linked_template(): void
    {
        $this->assertInstanceOf(Template::class, $this->banner_template->template);
    }

    /**
     * Test that the model can have zero linked configurations.
     */
    public function test_model_can_have_zero_linked_configurations(): void
    {
        $this->assertEmpty($this->banner_template->configurations);
    }

    /**
     * Test that the model has many linked configurations.
     */
    public function test_model_has_many_linked_configurations(): void
    {
        BannerConfiguration::factory(3)
            ->for($this->banner_template)
            ->create();

        $this->assertCount(3, $this->banner_template->configurations);
        $this->assertContainsOnlyInstancesOf(BannerConfiguration::class, $this->banner_template->configurations);
    }
}
