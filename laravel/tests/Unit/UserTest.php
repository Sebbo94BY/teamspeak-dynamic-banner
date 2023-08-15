<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
     * Test if the model can be crated.
     */
    public function test_model_can_be_created()
    {
        $user = User::factory()->create();

        $this->assertModelExists($user);
    }

    /**
     * Tests if a specific single role can be assigned to a user.
     */
    public function test_single_role_can_be_assigned_to_user()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $this->assertFalse($user->hasRole('Non Existing Role'));

        $this->assertTrue($user->hasRole('Super Admin'));
    }

    /**
     * Tests if multiple roles can be assigned to a user.
     */
    public function test_multiple_roles_can_be_assigned_to_user()
    {
        $user = User::factory()->create();
        $user->assignRole('Users Admin');
        $user->assignRole('Roles Admin');

        $this->assertFalse($user->hasRole('Non Existing Role'));

        $this->assertTrue($user->hasRole('Users Admin'));
        $this->assertTrue($user->hasRole('Roles Admin'));
    }

    /**
     * Tests if multiple roles can be synced to a user.
     */
    public function test_multiple_roles_can_be_synced_to_user()
    {
        $user = User::factory()->create();

        $user->syncRoles(['Templates Admin', 'Banners Admin']);

        $this->assertFalse($user->hasRole('Non Existing Role'));
        $this->assertFalse($user->hasRole('Users Admin'));

        $this->assertTrue($user->hasRole('Templates Admin'));
        $this->assertTrue($user->hasRole('Banners Admin'));
    }

    /**
     * Tests if a role assigned to a user grants the respective permissions.
     */
    public function test_role_assigns_permission_to_user()
    {
        $user = User::factory()->create();

        $user->syncRoles(['Users Admin', 'Banners Admin']);

        $this->assertFalse($user->hasPermissionTo('view roles'));
        $this->assertFalse($user->hasPermissionTo('view system status'));

        $this->assertTrue($user->hasPermissionTo('view users'));
        $this->assertTrue($user->hasPermissionTo('add users'));
        $this->assertTrue($user->hasPermissionTo('edit users'));
        $this->assertTrue($user->hasPermissionTo('delete users'));

        $this->assertTrue($user->hasPermissionTo('view banners'));
        $this->assertTrue($user->hasPermissionTo('add banners'));
        $this->assertTrue($user->hasPermissionTo('edit banners'));
        $this->assertTrue($user->hasPermissionTo('delete banners'));
    }
}
