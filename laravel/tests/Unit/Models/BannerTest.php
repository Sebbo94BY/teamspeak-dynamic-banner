<?php

namespace Tests\Unit\Models;

use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use RefreshDatabase;

    protected Instance $instance;

    protected Banner $banner;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->instance = Instance::factory()->create();

        $this->banner = Banner::factory()->for($this->instance)->create();
    }

    /**
     * Test that the model can be created.
     */
    public function test_model_can_be_created(): void
    {
        $this->assertModelExists($this->banner);
    }

    /**
     * Test that the model properly casts specific attributes.
     */
    public function test_model_properly_casts_specific_attributes(): void
    {
        $this->assertIsBool($this->banner->random_rotation);

        $this->banner->random_rotation = true;
        $this->banner->save();
        $this->assertIsBool($this->banner->random_rotation);
        $this->assertTrue($this->banner->random_rotation);

        $this->banner->random_rotation = 0;
        $this->banner->save();
        $this->assertIsBool($this->banner->random_rotation);
        $this->assertFalse($this->banner->random_rotation);
    }

    /**
     * Test that the model belongs to an instance.
     */
    public function test_model_belongs_to_an_instance(): void
    {
        $this->assertInstanceOf(Instance::class, $this->banner->instance);
    }

    /**
     * Test that the model can have zero linked templates.
     */
    public function test_model_can_have_zero_linked_templates(): void
    {
        $this->assertEmpty($this->banner->templates);
    }

    /**
     * Test that the model has one linked template.
     */
    public function test_model_has_one_linked_template(): void
    {
        BannerTemplate::factory()
            ->for($this->banner)
            ->for(Template::factory()->create())
            ->create();

        $this->assertCount(1, $this->banner->templates);
        $this->assertContainsOnlyInstancesOf(BannerTemplate::class, $this->banner->templates);
    }

    /**
     * Test that the model has many linked templates.
     */
    public function test_model_has_many_linked_templates(): void
    {
        BannerTemplate::factory()
            ->count(3)
            ->for($this->banner)
            ->for(Template::factory()->create())
            ->create();

        $this->assertCount(3, $this->banner->templates);
        $this->assertContainsOnlyInstancesOf(BannerTemplate::class, $this->banner->templates);
    }
}
