<?php

namespace Tests\Feature\Setup\Installer;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();
    }

    /**
     * Test that the installer user page can NOT be displayed, when there already exists at least one user, so it is no fresh installation.
     */
    public function test_redirect_to_dashboard_when_at_least_one_user_exists(): void
    {
        User::factory()->create();

        $response = $this->get(route('setup.installer.user'));
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test that the installer user page can be displayed, when it's a fresh installation.
     */
    public function test_view_gets_displayed(): void
    {
        $response = $this->from(route('setup.installer.requirements'))->get(route('setup.installer.user'));
        $response->assertStatus(200);
        $response->assertViewIs('setup.installer.user');
    }

    /**
     * Test that creating the initial user must match the request rules.
     */
    public function test_creating_the_initial_user_must_match_the_request_rules(): void
    {
        $response = $this->post(route('setup.installer.user.create'), [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(8), // password needs to be confirmed
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test that creating the initial user is only possible when no user exist yet.
     */
    public function test_creating_the_initial_user_is_only_possible_when_no_user_exist_yet(): void
    {
        $plain_password = fake()->password(8);

        $response = $this->post(route('setup.installer.user.create'), [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => $plain_password,
            'password_confirmation' => $plain_password,
        ]);
        $response->assertRedirectToRoute('dashboard');

        $second_response = $this->post(route('setup.installer.user.create'), [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => $plain_password,
            'password_confirmation' => $plain_password,
        ]);
        $second_response->assertStatus(302);
    }

    /**
     * Test that creating the initial user gets the super admin role.
     */
    public function test_creating_the_initial_user_gets_the_super_admin_role(): void
    {
        $plain_password = fake()->password(8);

        $this->post(route('setup.installer.user.create'), [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => $plain_password,
            'password_confirmation' => $plain_password,
        ]);

        $this->assertTrue(User::first()->hasRole('Super Admin'));
    }

    /**
     * Test that creating the initial user finishes the installer.
     */
    public function test_creating_the_initial_user_finishes_the_installer(): void
    {
        $plain_password = fake()->password(8);

        $response = $this->post(route('setup.installer.user.create'), [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => $plain_password,
            'password_confirmation' => $plain_password,
        ]);
        $response->assertRedirectToRoute('dashboard');
        $response->assertLocation(route('dashboard'));
        $response->assertSessionHas('success');
    }
}
