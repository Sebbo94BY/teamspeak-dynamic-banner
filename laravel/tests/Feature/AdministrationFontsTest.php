<?php

namespace Tests\Feature;

use App\Models\Font;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdministrationFontsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Properties / Settings
     */
    protected string $upload_directory = 'uploads/fonts';

    protected User $user;

    protected Font $font;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->user = User::factory()->create();
        $this->user->syncRoles('Fonts Admin');

        $this->font = Font::factory()->create();
    }

    /**
     * Test, that the user gets redirected to the login, when he is unauthenticated.
     */
    public function test_user_gets_redirected_to_login_when_unauthenticated(): void
    {
        $response = $this->get(route('administration.fonts'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test, that the user can access the page, when he is authenticated.
     */
    public function test_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('administration.fonts'));
        $response->assertStatus(200);
        $response->assertViewIs('administration.fonts');
    }

//    /**
//     * Test, that the user can access the "add font" page, when he is authenticated.
//     */
//    public function test_add_font_page_gets_displayed_when_authenticated(): void
//    {
//        //TODO obsolete
//        $response = $this->actingAs($this->user)->get(route('administration.font.add'));
//        $response->assertStatus(200);
//        $response->assertViewIs('administration.font.add');
//    }

    /**
     * Test that adding a new font successfully returns the view.
     */
    public function test_adding_a_new_font_successfully_returns_the_view(): void
    {
        $response = $this->actingAs($this->user)->post(route('administration.font.create'), [
            'file' => UploadedFile::fake()->create('some-font.ttf', fake()->numberBetween(64, 256), 'font/ttf'),
        ]);
        $response->assertRedirectToRoute('administration.fonts');
        $response->assertSessionHas('success');
    }

    /**
     * Test that trying to edit a font ID, which exists, returns the respective view.
     */
//    public function test_edit_font_returns_the_edit_form_when_the_given_id_exists(): void
//    {
//        //todo obsolete
//        $response = $this->actingAs($this->user)->get(route('administration.font.edit', ['font_id' => $this->font->id]));
//        $response->assertStatus(200);
//        $response->assertViewIs('administration.font.edit');
//        $response->assertViewHas('font');
//    }

    /**
     * Test that updating an existing font is working as expected.
     */
    public function test_updating_an_existing_font_is_working_as_expected(): void
    {
        $other_font = Font::factory()->create();
        file_put_contents(
            public_path($this->upload_directory.DIRECTORY_SEPARATOR.$other_font->filename),
            fake()->text()
        );

        $response = $this->actingAs($this->user)->patch(route('administration.font.update', ['font_id' => $other_font->id]), [
            'file' => UploadedFile::fake()->create('another-font.ttf', fake()->numberBetween(64, 256), 'font/ttf'),
        ]);
        $response->assertRedirectToRoute('administration.fonts');
        $response->assertSessionHas('success');
    }

    /**
     * Test that trying to delete a font ID, which exists, returns the respective view.
     */
    public function test_delete_font_returns_the_edit_form_when_the_given_id_exists(): void
    {
        $other_font = Font::factory()->create();
        file_put_contents(
            public_path($this->upload_directory.DIRECTORY_SEPARATOR.$other_font->filename),
            fake()->text()
        );

        $response = $this->actingAs($this->user)->delete(route('administration.font.delete', ['font_id' => $other_font->id]));
        $response->assertRedirectToRoute('administration.fonts');
        $response->assertSessionHas('success');
        $this->assertFileDoesNotExist(public_path($this->upload_directory.DIRECTORY_SEPARATOR.$other_font->filename));
    }
}
