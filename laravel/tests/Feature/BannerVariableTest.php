<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Instance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerVariableTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Banner $banner;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->user = User::factory()->create();

        $this->banner = Banner::factory()->for(
            Instance::factory()->create()
        )->create();
    }

    /**
     * Test, that the user gets redirected to the login, when he is unauthenticated.
     */
    public function test_user_gets_redirected_to_login_when_unauthenticated(): void
    {
        $response = $this->get(route('banner.variables', ['banner_id' => $this->banner->id]));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test, that the user gets redirected to the overview page, when the given banner ID does not exist.
     */
    public function test_user_gets_redirected_when_banner_id_does_not_exist(): void
    {
        $response = $this->actingAs($this->user)->get(route('banner.variables', ['banner_id' => 1337]));
        $response->assertRedirect(route('banners'));
    }

    /**
     * Test that the view gets displayed.
     */
    public function test_user_can_view_the_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('banner.variables', ['banner_id' => $this->banner->id]));
        $response->assertViewIs('banner.variables');
        $response->assertViewHas('redis_connection_error');
        $response->assertViewHas('variables_and_values');
    }
}
