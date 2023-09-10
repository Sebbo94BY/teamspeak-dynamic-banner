<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
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
     * Test that adding a new user requires to match the request rules.
     */
    public function test_adding_a_new_user_requires_to_match_the_request_rules(): void
    {
        $response = $this->actingAs($this->user)->post(route('administration.user.create'), [
            'email' => fake()->email(),
            'roles' => [1, 2, 8],
        ]);
        $response->assertSessionHasErrors(['name']);

        $response = $this->actingAs($this->user)->post(route('administration.user.create'), [
            'name' => fake()->name(),
            'email' => fake()->email(),
        ]);
        $response->assertSessionHasErrors(['roles']);

        $response = $this->actingAs($this->user)->post(route('administration.user.create'), [
            'name' => fake()->name(),
            'roles' => [1, 2, 8],
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test that adding a new user successfully assigns the respective roles.
     */
    public function test_adding_a_new_user_successfully_assigns_the_respective_roles(): void
    {
        $this->actingAs($this->user)->post(route('administration.user.create'), [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'roles' => [
                Role::where('name', '=', 'System Status Viewer')->first()->id,
                Role::where('name', '=', 'PHP Info Viewer')->first()->id,
            ],
        ]);

        $new_user = User::whereNotIn('id', [$this->user->id])->first();

        $this->assertTrue($new_user->hasRole('System Status Viewer'));
        $this->assertTrue($new_user->hasRole('PHP Info Viewer'));
        $this->assertFalse($new_user->hasRole('Users Admin'));
    }

    /**
     * Test that adding a new user successfully returns the view.
     */
    public function test_adding_a_new_user_successfully_returns_the_view(): void
    {
        $response = $this->actingAs($this->user)->post(route('administration.user.create'), [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'roles' => [
                Role::where('name', '=', 'System Status Viewer')->first()->id,
            ],
        ]);
        $response->assertRedirectToRoute('administration.users');
        $response->assertSessionHas('success');
    }

    /**
     * Test that updating an existing user requires to match the request rules.
     */
    public function test_updating_an_existing_user_requires_to_match_the_request_rules(): void
    {
        $other_user = User::factory()->create();

        $response = $this->actingAs($this->user)->patch(route('administration.user.update', ['user_id' => $other_user->id]), [
            'email' => fake()->email(),
            'roles' => [1, 2, 8],
        ]);
        $response->assertSessionHasErrors(['name']);

        $response = $this->actingAs($this->user)->patch(route('administration.user.update', ['user_id' => $other_user->id]), [
            'name' => fake()->name(),
            'email' => fake()->email(),
        ]);
        $response->assertSessionHasErrors(['roles']);

        $response = $this->actingAs($this->user)->patch(route('administration.user.update', ['user_id' => $other_user->id]), [
            'name' => fake()->name(),
            'roles' => [1, 2, 8],
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test that updating an existing user successfully assigns the respective roles.
     */
    public function test_updating_an_existing_user_successfully_assigns_the_respective_roles(): void
    {
        $other_user = User::factory()->create();

        $this->actingAs($this->user)->patch(route('administration.user.update', ['user_id' => $other_user->id]), [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'roles' => [
                Role::where('name', '=', 'System Status Viewer')->first()->id,
                Role::where('name', '=', 'PHP Info Viewer')->first()->id,
            ],
        ]);

        $this->assertTrue($other_user->hasRole('System Status Viewer'));
        $this->assertTrue($other_user->hasRole('PHP Info Viewer'));
        $this->assertFalse($other_user->hasRole('Users Admin'));
    }

    /**
     * Test that updating an existing user is working as expected.
     */
    public function test_updating_an_existing_user_is_working_as_expected(): void
    {
        $other_user = User::factory()->create();

        $response = $this->actingAs($this->user)->patch(route('administration.user.update', ['user_id' => $other_user->id]), [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'roles' => [1, 2, 8],
        ]);
        $response->assertRedirectToRoute('administration.users');
        $response->assertSessionHas('success');
    }

    /**
     * Test that trying to delete a user ID, which exists, returns the respective view.
     */
    public function test_delete_user_returns_the_edit_form_when_the_given_uid_exists(): void
    {
        $other_user = User::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('administration.user.delete', ['user_id' => $other_user->id]));
        $response->assertRedirectToRoute('administration.users');
        $response->assertSessionHas('success');
    }
}
