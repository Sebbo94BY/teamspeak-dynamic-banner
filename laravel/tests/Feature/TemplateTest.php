<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->user = User::factory()->create();
        $this->user->syncRoles('Templates Admin');
    }

    /**
     * Test, that the user gets redirected to the login, when he is unauthenticated.
     */
    public function test_user_gets_redirected_to_login_when_unauthenticated(): void
    {
        $response = $this->get(route('templates'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test, that the user can access the page, when he is authenticated.
     */
    public function test_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('templates'));
        $response->assertStatus(200);
        $response->assertViewIs('templates');
    }

    /**
     * Test, that the user can access the "add template" page, when he is authenticated.
     */
    public function test_add_template_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('template.add'));
        $response->assertStatus(200);
        $response->assertViewIs('template.add');
    }

    /**
     * Test, that the user gets redirected to the templates overview, when the requested template ID for the edit page does not exist.
     */
    public function test_edit_template_page_gets_redirected_to_overview_when_template_id_does_not_exist(): void
    {
        $response = $this->actingAs($this->user)->get(route('template.edit', ['template_id' => 1337]));
        $response->assertRedirect(route('templates'));
    }

    /**
     * Test, that the user can access the "edit template" page, when the requested template ID for the edit page exists.
     */
    public function test_edit_template_page_gets_displayed_when_template_id_exists(): void
    {
        $template = Template::factory()->create();

        $response = $this->actingAs($this->user)->get(route('template.edit', ['template_id' => $template->id]));
        $response->assertViewIs('template.edit');
        $response->assertViewHas('template');
    }
}
