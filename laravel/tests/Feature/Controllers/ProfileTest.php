<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected string $initial_user_password = 'myP4ssw0rd';

    protected string $new_password = 'veryS$cretP4ssw0rd!';

    protected User $user;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['password' => Hash::make($this->initial_user_password)]);
    }

    /**
     * Test that the user can update his profile information.
     */
    public function test_user_can_update_his_profile_information(): void
    {
        $response = $this->actingAs($this->user)->patch(route('profile.update'), [
            'name' => 'John Doe',
            'email' => fake()->email(),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirectToRoute('profile.edit');
        $response->assertSessionHas('success');
    }

    /**
     * Test that the user can not update his email address to an identical one of a different user.
     */
    public function test_user_can_not_update_his_email_to_an_identical_one_of_a_different_user(): void
    {
        $other_user = User::factory()->create();

        $response = $this->actingAs($other_user)->patch(route('profile.update'), [
            'name' => $other_user->name,
            'email' => $this->user->email,
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test that the user can not change his password without the current one.
     */
    public function test_user_can_not_change_his_password_without_the_current_one(): void
    {
        $response = $this->actingAs($this->user)->patch(route('profile.update.password'), [
            'current_password' => 'wrong-current-password',
            'password' => $this->new_password,
            'password_confirmation' => $this->new_password,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * Test that the user has to confirm the new password.
     */
    public function test_user_has_to_confirm_the_new_password(): void
    {
        $response = $this->actingAs($this->user)->patch(route('profile.update.password'), [
            'current_password' => $this->initial_user_password,
            'password' => $this->new_password,
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test that the user can change his password.
     */
    public function test_user_can_change_his_password(): void
    {
        $this->new_password = 'veryS$cretP4ssw0rd!';

        $response = $this->actingAs($this->user)->patch(route('profile.update.password'), [
            'current_password' => $this->initial_user_password,
            'password' => $this->new_password,
            'password_confirmation' => $this->new_password,
        ]);

        $response->assertRedirectToRoute('profile.edit');
        $response->assertSessionHas('success');
    }
}
