<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectIfAuthenticatedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test, that the user does NOT get redirected to the HOME route if unauthenticated.
     */
    public function test_no_redirect_to_dashboard_if_unauthenticated(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    /**
     * Test, that the user gets redirected to the HOME route if authenticated.
     */
    public function test_redirect_to_dashboard_if_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
