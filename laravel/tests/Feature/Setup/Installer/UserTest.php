<?php

namespace Tests\Feature\Setup\Installer;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test, that the installer user page can be displayed, when it's a fresh installation.
     */
    public function test_view_gets_displayed(): void
    {
        $response = $this->from('/setup/installer/requirements')->get(route('setup.installer.user'));
        $response->assertStatus(200);
        $response->assertViewIs('setup.installer.user');
    }
}
