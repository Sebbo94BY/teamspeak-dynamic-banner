<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->user = User::factory()->create();
    }

    /**
     * Test that the model can be created.
     */
    public function test_model_can_be_created(): void
    {
        $this->assertModelExists($this->user);
    }

    /**
     * Test that the model properly hides specific attributes in serializations.
     */
    public function test_model_properly_hides_specific_attributes_in_serializations(): void
    {
        $this->assertArrayHasKey('name', $this->user->toArray());
        $this->assertArrayHasKey('email', $this->user->toArray());

        $this->assertArrayNotHasKey('password', $this->user->toArray());
        $this->assertArrayNotHasKey('remember_token', $this->user->toArray());
    }

    /**
     * Test that the model properly casts specific attributes.
     */
    public function test_model_properly_casts_specific_attributes(): void
    {
        $this->assertInstanceOf(Carbon::class, $this->user->email_verified_at);
    }

    /**
     * Test that a specific single role can be assigned to a user.
     */
    public function test_single_role_can_be_assigned_to_user()
    {
        $this->user->assignRole('Super Admin');

        $this->assertFalse($this->user->hasRole('Non Existing Role'));

        $this->assertTrue($this->user->hasRole('Super Admin'));
    }

    /**
     * Test that multiple roles can be assigned to a user.
     */
    public function test_multiple_roles_can_be_assigned_to_user()
    {
        $this->user->assignRole('Users Admin');
        $this->user->assignRole('Roles Admin');

        $this->assertFalse($this->user->hasRole('Non Existing Role'));

        $this->assertTrue($this->user->hasRole('Users Admin'));
        $this->assertTrue($this->user->hasRole('Roles Admin'));
    }

    /**
     * Test that multiple roles can be synced to a user.
     */
    public function test_multiple_roles_can_be_synced_to_user()
    {
        $this->user->syncRoles(['Templates Admin', 'Banners Admin']);

        $this->assertFalse($this->user->hasRole('Non Existing Role'));
        $this->assertFalse($this->user->hasRole('Users Admin'));

        $this->assertTrue($this->user->hasRole('Templates Admin'));
        $this->assertTrue($this->user->hasRole('Banners Admin'));
    }

    /**
     * Tests if a role assigned to a user grants the respective permissions.
     */
    public function test_role_assigns_permission_to_user()
    {
        $this->user->syncRoles(['Users Admin', 'Banners Admin']);

        $this->assertFalse($this->user->hasPermissionTo('view roles'));
        $this->assertFalse($this->user->hasPermissionTo('view system status'));

        $this->assertTrue($this->user->hasPermissionTo('view users'));
        $this->assertTrue($this->user->hasPermissionTo('add users'));
        $this->assertTrue($this->user->hasPermissionTo('edit users'));
        $this->assertTrue($this->user->hasPermissionTo('delete users'));

        $this->assertTrue($this->user->hasPermissionTo('view banners'));
        $this->assertTrue($this->user->hasPermissionTo('add banners'));
        $this->assertTrue($this->user->hasPermissionTo('edit banners'));
        $this->assertTrue($this->user->hasPermissionTo('delete banners'));
    }
}
