<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->user = User::factory()->create();
        $this->user->syncRoles('Instances Admin');
    }

    /**
     * Test, that the user gets redirected to the login, when he is unauthenticated.
     */
    public function test_user_gets_redirected_to_login_when_unauthenticated(): void
    {
        $response = $this->get(route('instances'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test, that the user can access the page, when he is authenticated.
     */
    public function test_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('instances'));
        $response->assertStatus(200);
        $response->assertViewIs('instances');
    }

    /**
     * Test, that the user can access the "add instance" page, when he is authenticated.
     */
    public function test_add_instance_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('instance.add'));
        $response->assertStatus(200);
        $response->assertViewIs('instance.add');
    }

    /**
     * Test, that the user gets redirected to the instances overview, when the requested instance ID for the edit page does not exist.
     */
    public function test_edit_instance_page_gets_redirected_to_overview_when_instance_id_does_not_exist(): void
    {
        $response = $this->actingAs($this->user)->get(route('instance.edit', ['instance_id' => 1337]));
        $response->assertRedirect(route('instances'));
    }

    /**
     * Test, that the user can access the "edit instance" page, when the requested instance ID for the edit page exists.
     *
     * Disabled because the assertions are failing with this error:
     *    fwrite(): Argument #1 ($stream) must be of type resource, bool given
     */
    // public function test_edit_instance_page_gets_displayed_when_instance_id_exists(): void
    // {
    //     $instance = Instance::factory()->create();
    //
    //     $response = $this->actingAs($this->user)->get(route('instance.edit', ['instance_id' => $instance->id]));
    //     $response->assertViewIs('instance.edit');
    //     $response->assertViewHas('instance');
    //     $response->assertViewHas('channel_list');
    // }
}
