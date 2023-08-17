<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdministrationUsersTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->user = User::factory()->create();
        $this->user->syncRoles('Users Admin');
    }

    /**
     * Test, that the user gets redirected to the login, when he is unauthenticated.
     */
    public function test_user_gets_redirected_to_login_when_unauthenticated(): void
    {
        $response = $this->get(route('administration.users'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test, that the user can access the page, when he is authenticated.
     */
    public function test_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('administration.users'));
        $response->assertStatus(200);
        $response->assertViewIs('administration.users');
    }

    /**
     * Test, that the user can access the "add user" page, when he is authenticated.
     */
    public function test_add_user_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('administration.user.add'));
        $response->assertStatus(200);
        $response->assertViewIs('administration.user.add');
        $response->assertViewHas('roles');
    }
}
