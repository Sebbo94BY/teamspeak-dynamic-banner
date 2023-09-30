<?php

namespace Tests\Unit\Models;

use App\Models\Localization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected Localization $localization;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->localization = Localization::factory()->create();
    }

    /**
     * Test that the model can be created.
     */
    public function test_model_can_be_created(): void
    {
        $this->assertModelExists($this->localization);
    }
}
