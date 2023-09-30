<?php

namespace Tests\Feature;

use App\Models\Localization;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Template $template;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->user = User::factory()->for(Localization::factory()->create())->create();
        $this->user->syncRoles('Templates Admin');

        $this->template = Template::factory()->create();
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
        // delete the global default template for this test as we otherwise receive a ViewException since the actual image does not exist
        $this->template->delete();
        $response = $this->actingAs($this->user)->get(route('templates'));
        $response->assertStatus(200);
        $response->assertViewIs('templates');
    }

    /**
     * Test that adding a new template requires to match the request rules.
     */
    public function test_adding_a_new_template_requires_to_match_the_request_rules(): void
    {
        $response = $this->actingAs($this->user)->post(route('template.save'), [
            'alias' => fake()->name(),
        ]);
        $response->assertSessionHasErrors(['file']);
    }

    /**
     * Test that updating an existing template requires to match the request rules.
     */
    public function test_updating_an_existing_template_requires_to_match_the_request_rules(): void
    {
        $response = $this->actingAs($this->user)->patch(route('template.update', ['template_id' => $this->template->id]), [
            'alias' => '',
        ]);
        $response->assertSessionHasErrors(['alias']);
    }
}
