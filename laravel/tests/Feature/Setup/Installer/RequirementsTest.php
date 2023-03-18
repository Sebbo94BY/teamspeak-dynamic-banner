<?php

namespace Tests\Feature\Setup\Installer;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequirementsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test, that the installer requirements page can be displayed, when it's a fresh installation.
     */
    public function test_view_gets_displayed(): void
    {
        $response = $this->get(route('setup.installer.requirements'));
        $response->assertStatus(200);
        $response->assertViewIs('setup.installer.requirements');
    }
}
