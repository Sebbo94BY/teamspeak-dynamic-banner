<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Tests\TestCase;

class WebAuthRouteTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();
    }

    public function test_login_route_exists()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function test_registration_route_is_missing()
    {
        $response = $this->get('/register');
        $response->assertStatus(404);

        $this->expectException(RouteNotFoundException::class);
        $response = $this->get(route('register'));
        $response->assertStatus(404);
    }

    public function test_can_view_forgot_password()
    {
        $response = $this->get(Route('password.request'));
        $response->assertStatus(200);
    }
}
