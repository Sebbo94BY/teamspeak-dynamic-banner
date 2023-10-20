<?php

namespace Tests\Feature\API\v1;

use App\Models\Banner;
use App\Models\BannerConfiguration;
use App\Models\BannerTemplate;
use App\Models\Font;
use App\Models\Instance;
use App\Models\Template;
use App\Models\TwitchApi;
use App\Models\TwitchStreamer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Class properties
     */
    protected string $upload_directory = 'uploads/fonts';

    protected Banner $banner;

    public function setUp(): void
    {
        parent::setUp();

        $this->banner = Banner::factory()->for(
            Instance::factory()->create()
        )->create();
    }

    /**
     * Ensure, that the API endpoint requires a banner ID as parameter.
     */
    public function test_api_requires_banner_id_parameter(): void
    {
        $this->expectExceptionMessageMatches('/Missing required parameter/i');
        $response = $this->get(route('api.banner'));
        $response->assertStatus(404);
    }

    /**
     * Ensure, that the API endpoint requires an existing banner ID as parameter.
     */
    public function test_api_requires_existing_banner_id_parameter(): void
    {
        $response = $this->get(route('api.banner', ['banner_id' => 'abc']));
        $response->assertSeeText('Invalid Banner ID in the URL.');
        $response->assertStatus(404);

        $response = $this->get(route('api.banner', ['banner_id' => 1337]));
        $response->assertSeeText('Invalid Banner ID in the URL.');
        $response->assertStatus(404);
    }

    /**
     * Ensure, that the API endpoint returns an error, when it has no linked templates at all.
     */
    public function test_api_returns_error_when_no_templates_are_linked(): void
    {
        $response = $this->get(route('api.banner', ['banner_id' => base_convert($this->banner->id, 10, 35)]));
        $response->assertSeeText('The banner does either not have any configured templates or all of them are disabled.');
        $response->assertStatus(401);
    }

    /**
     * Ensure, that the API endpoint returns an error, when the given banner template ID in the URL does not exist.
     */
    public function test_api_returns_error_when_banner_template_id_in_url_does_not_exist(): void
    {
        $banner_template = BannerTemplate::factory()
            ->for(
                $this->banner
            )->for(
                Template::factory()->create()
            )->create();
        $banner_template->enabled = true;
        $banner_template->save();

        $response = $this->get(route('api.banner', ['banner_id' => base_convert($this->banner->id, 10, 35), 'banner_template_id' => 1337]));
        $response->assertSeeText('Invalid Banner Template ID in the URL.');
        $response->assertStatus(404);
    }

    /**
     * Ensure, that the API endpoint returns an error, when the template has no configuration at all.
     */
    public function test_api_returns_error_when_the_template_has_no_configuration(): void
    {
        $banner_template = BannerTemplate::factory()
            ->for(
                $this->banner
            )->for(
                Template::factory()->create()
            )->create();
        $banner_template->enabled = true;
        $banner_template->save();

        $response = $this->get(route('api.banner', ['banner_id' => base_convert($this->banner->id, 10, 35)]));
        $response->assertSeeText('The template does not have any configurations. This seems wrong.');
        $response->assertStatus(500);
    }

    /**
     * Ensure, that the API endpoint returns an image with respective HTTP headers when everything is fine.
     */
    public function test_api_returns_an_image_with_respective_http_headers_when_everything_is_fine(): void
    {
        TwitchApi::factory()->create();

        $banner_configuration = BannerConfiguration::factory()
            ->for(
                $banner_template = BannerTemplate::factory()
                    ->for(
                        $this->banner
                    )->for(
                        Template::factory()->create()
                    )->for(
                        TwitchStreamer::factory()->create(), 'twitch_streamer'
                    )->create(['enabled' => true])
            )
            ->for(Font::factory()->create())
            ->create();

        // Overwrite the factory default to match the actual controller path
        $banner_template->file_path_drawed_text = 'uploads/templates/drawed_text/'.$banner_template->id;

        // Generate a temporary image to be able to test the API
        $absolut_upload_directory = public_path($banner_template->template->file_path_original);
        $image_file_path = $absolut_upload_directory.'/'.$banner_template->template->filename;
        $gd_image = imagecreate(1024, 300);
        imagecolorallocate($gd_image, 0, 0, 0);
        if (! file_exists($absolut_upload_directory)) {
            mkdir($absolut_upload_directory, 0777, true);
        }
        $absolut_drawed_text_directory = public_path($banner_template->file_path_drawed_text).'/'.$banner_template->template->filename;
        if (! file_exists($absolut_drawed_text_directory)) {
            mkdir($absolut_drawed_text_directory, 0777, true);
        }
        imagepng($gd_image, $image_file_path);

        // Temporary download a TTF fontfile to be able test the API
        Storage::disk('public')->put($this->upload_directory.DIRECTORY_SEPARATOR.$banner_configuration->font->filename, file_get_contents('https://api.fontsource.org/v1/fonts/abel/latin-400-normal.ttf'));

        $response = $this->get(route('api.banner', ['banner_id' => base_convert($this->banner->id, 10, 35)]));
        $response->assertHeader('Cache-Control');
        $response->assertHeader('Expires', '-1');
        $response->assertHeader('ETag');
        $response->assertHeader('Last-Modified');
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/jpeg', 'image/gif']);
        $response->assertStatus(200);

        // Delete temporary files again
        Storage::disk('public')->delete($this->upload_directory.DIRECTORY_SEPARATOR.$banner_configuration->font->filename);
        unlink($image_file_path);
    }

    /**
     * Test that the redirect URL API returns an error when an not existing banner ID has been provided.
     */
    public function test_redirect_url_api_returns_an_error_when_an_not_existing_banner_id_has_been_provided(): void
    {
        $response = $this->get(route('api.banner.redirect_url', ['banner_id' => 'nonExistingId']));
        $response->assertSeeText('Invalid Banner ID in the URL.');
        $response->assertStatus(404);
    }

    /**
     * Test that the redirect URL API redirects to the banner image when no URL has been configured.
     */
    public function test_redirect_url_api_redirects_to_the_banner_image_when_no_url_has_been_configured(): void
    {
        BannerConfiguration::factory()
            ->for(
                BannerTemplate::factory()
                    ->for(
                        $this->banner
                    )->for(
                        Template::factory()->create()
                    )->create(['redirect_url' => null, 'enabled' => true])
            )
            ->for(Font::factory()->create())
            ->create();

        $response = $this->get(route('api.banner.redirect_url', ['banner_id' => base_convert($this->banner->id, 10, 35)]));
        $response->assertRedirectToRoute('api.banner', ['banner_id' => base_convert($this->banner->id, 35, 10)]);
        $response->assertStatus(302);
    }

    /**
     * Test that the redirect URL API redirects to the configured URL when an URL has been configured.
     */
    public function test_redirect_url_api_redirects_to_the_configure_url_when_an_url_has_been_configured(): void
    {
        $redirect_url = 'https://localhost/test-redirect';

        BannerConfiguration::factory()
            ->for(
                BannerTemplate::factory()
                    ->for(
                        $this->banner
                    )->for(
                        Template::factory()->create()
                    )->create(['redirect_url' => $redirect_url, 'enabled' => true])
            )
            ->for(Font::factory()->create())
            ->create();

        $response = $this->get(route('api.banner.redirect_url', ['banner_id' => base_convert($this->banner->id, 10, 35)]));
        $response->assertRedirect($redirect_url);
        $response->assertStatus(302);
    }
}
