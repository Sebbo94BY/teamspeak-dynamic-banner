<?php

namespace Tests\Feature\Middleware;

use App\Models\Localization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test, that the user gets redirected to the login if at least one user exists and is not authenticated.
     */
    public function test_redirect_to_login_if_unauthenticated(): void
    {
        User::factory()->for(Localization::factory()->create())->create();

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test, that the user gets redirected to the installer if no user exists yet.
     */
    public function test_redirect_to_installer_if_no_user_exists(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('setup.installer.requirements'));
    }
}
