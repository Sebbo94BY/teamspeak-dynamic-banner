<?php

namespace Tests\Unit\Models;

use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

    protected Template $template;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->template = Template::factory()->create();
    }

    /**
     * Test that the model can be created.
     */
    public function test_model_can_be_created(): void
    {
        $this->assertModelExists($this->template);
    }

    /**
     * Test that the model can have zero linked banner templates.
     */
    public function test_model_can_have_zero_linked_banner_templates(): void
    {
        $this->assertEmpty($this->template->banner_templates);
    }

    /**
     * Test that the model relationship `isUsedByBannerId` returns boolean.
     */
    public function test_model_relationship_isUsedByBannerId_returns_boolean(): void
    {
        $banner = Banner::factory()->for(
            Instance::factory()->create()
        )->create();

        BannerTemplate::factory()
            ->for($banner)
            ->for($this->template)
            ->create();

        $this->assertIsBool($this->template->isUsedByBannerId($banner->id));
        $this->assertTrue($this->template->isUsedByBannerId($banner->id));
    }

    /**
     * Test that the model relationship `isEnabledForBannerId` returns boolean.
     */
    public function test_model_relationship_isEnabledForBannerId_returns_boolean(): void
    {
        $banner = Banner::factory()->for(
            Instance::factory()->create()
        )->create();

        BannerTemplate::factory()
            ->for($banner)
            ->for($this->template)
            ->create();

        $this->assertIsBool($this->template->isEnabledForBannerId($banner->id));
    }

    /**
     * Test that the model can have one linked banner template.
     */
    public function test_model_can_have_one_linked_banner_template(): void
    {
        BannerTemplate::factory()
            ->count(1)
            ->for(
                Banner::factory()->for(
                    Instance::factory()->create()
                )->create()
            )
            ->for($this->template)
            ->create();

        $this->assertCount(1, $this->template->banner_templates);
        $this->assertContainsOnlyInstancesOf(BannerTemplate::class, $this->template->banner_templates);
    }

    /**
     * Test that the model can have multiple linked banner templates.
     */
    public function test_model_can_have_multiple_linked_banner_templates(): void
    {
        BannerTemplate::factory()
            ->count(3)
            ->for(
                Banner::factory()->for(
                    Instance::factory()->create()
                )->create()
            )
            ->for($this->template)
            ->create();

        $this->assertCount(3, $this->template->banner_templates);
        $this->assertContainsOnlyInstancesOf(BannerTemplate::class, $this->template->banner_templates);
    }
}
