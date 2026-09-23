<?php

namespace Tests\Feature;

use App\Http\Controllers\TemplateController;
use App\Jobs\DrawGridSystemOnTemplate;
use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Localization;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Template $template;

    protected function setUp(): void
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
     * Test that the unused-template filter excludes templates assigned to a banner.
     */
    public function test_unused_template_filter_only_returns_unassigned_templates(): void
    {
        $assignedTemplate = Template::factory()->create();
        BannerTemplate::factory()
            ->for(Banner::factory()->for(Instance::factory()->create())->create())
            ->for($assignedTemplate)
            ->create();

        $view = app(TemplateController::class)->overview(Request::create('/templates', 'GET', ['attention' => 'unused']));
        $templates = $view->getData()['templates'];

        $this->assertTrue($templates->contains('id', $this->template->id));
        $this->assertFalse($templates->contains('id', $assignedTemplate->id));
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
     * Test that a valid image is stored and its dimensions are persisted.
     */
    public function test_adding_a_valid_template_stores_its_actual_dimensions(): void
    {
        Queue::fake();
        File::ensureDirectoryExists(public_path('uploads/templates'));

        $response = $this->actingAs($this->user)->post(route('template.save'), [
            'alias' => 'Status banner',
            'file' => UploadedFile::fake()->image('status-banner.png', 468, 60),
        ]);

        $response->assertRedirectToRoute('templates');
        $response->assertSessionHas('success');

        $stored_template = Template::query()->latest('id')->firstOrFail();
        $this->assertSame('Status banner', $stored_template->alias);
        $this->assertSame(468, $stored_template->width);
        $this->assertSame(60, $stored_template->height);
        $this->assertFileExists(public_path($stored_template->file_path_original.'/'.$stored_template->filename));
        Queue::assertPushed(DrawGridSystemOnTemplate::class);

        unlink(public_path($stored_template->file_path_original.'/'.$stored_template->filename));
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
