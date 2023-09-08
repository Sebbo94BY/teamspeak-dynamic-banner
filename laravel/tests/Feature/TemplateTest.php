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

    protected Template $template;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->user = User::factory()->create();
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

//    /**
//     * Test, that the user can access the "add template" page, when he is authenticated.
//     */
//    public function test_add_template_page_gets_displayed_when_authenticated(): void
//    {
//        //todo obsolete
//        $response = $this->actingAs($this->user)->get(route('template.add'));
//        $response->assertStatus(200);
//        $response->assertViewIs('template.add');
//    }

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

//    /**
//     * Test, that the user can access the "edit template" page, when the requested template ID for the edit page exists.
//     */
//    public function test_edit_template_page_gets_displayed_when_template_id_exists(): void
//    {
//        //todo obsolete
//        $response = $this->actingAs($this->user)->get(route('template.edit', ['template_id' => $this->template->id]));
//        $response->assertViewIs('template.edit');
//        $response->assertViewHas('template');
//    }

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
