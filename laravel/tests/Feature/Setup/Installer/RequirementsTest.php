<?php

namespace Tests\Feature\Setup\Installer;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequirementsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test, that the installer requirements page can NOT be displayed, when there already exists at least one user, so it is no fresh installation.
     */
    public function test_redirect_to_dashboard_when_at_least_one_user_exists(): void
    {
        User::factory()->create();

        $response = $this->get(route('setup.installer.requirements'));
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test, that the installer requirements page can be displayed, when it's a fresh installation.
     */
    public function test_view_gets_displayed(): void
    {
        $response = $this->get(route('setup.installer.requirements'));
        $response->assertStatus(200);
        $response->assertViewIs('administration.systemstatus');
    }
}
