<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\DrawTextOnTemplateController;
use App\Models\Banner;
use App\Models\BannerTemplate;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BannerController extends Controller
{
    /**
     * Class properties
     */
    private Banner $banner;

    private Collection|BannerTemplate $banner_templates;

    private ?BannerTemplate $selected_banner_template;

    private ?string $response_text = null;

    private int $response_code;

    /**
     * The class constructor
     */
    public function __construct(Request $request)
    {
        try {
            $this->banner = Banner::findOrFail(base_convert($request->banner_id, 35, 10));
        } catch (ModelNotFoundException) {
            $this->response_text = 'Invalid Banner ID in the URL.';
            $this->response_code = 404;

            return;
        }

        $this->banner_templates = BannerTemplate::where(['banner_id' => $this->banner->id, 'enabled' => true])->get();

        if ($this->banner_templates->count() == 0) {
            $this->response_text = 'The banner does either not have any configured templates or all of them are disabled.';
            $this->response_code = 401;

            return;
        }

        if ($request->has('banner_template_id')) {
            $this->selected_banner_template = $this->banner_templates->where('id', '=', $request->banner_template_id)->first();

            if (is_null($this->selected_banner_template)) {
                $this->response_text = 'Invalid Banner Template ID in the URL.';
                $this->response_code = 404;

                return;
            }
        } elseif ($this->banner->random_rotation) {
            $this->selected_banner_template = $this->banner_templates->random(1)->first();
        } else {
            if ($this->banner_templates->count() == 1) {
                $this->selected_banner_template = $this->banner_templates[0];
            } else {
                $current_minute = Carbon::now()->format('i');
                $display_duration_per_template = intval(60 / $this->banner_templates->count());
                $this->selected_banner_template = $this->banner_templates[abs(intval(ceil($current_minute / $display_duration_per_template)) - 1)];
            }
        }

        if (count($this->selected_banner_template->configurations) == 0) {
            $this->response_text = 'The template does not have any configurations. This seems wrong.';
            $this->response_code = 500;

            return;
        }
    }

    /**
     * Return the rendered template.
     */
    public function get_rendered_template(Request $request)
    {
        if (! is_null($this->response_text)) {
            return response($this->response_text, $this->response_code);
        }

        // Set headers to e. g. avoid caching
        $current_rfc7231_datetime = Carbon::now()->subSeconds(5)->toRfc7231String();
        header('Cache-Control: no-cache');
        header('Expires: -1');
        header('ETag: '.md5($current_rfc7231_datetime));
        header('Last-Modified: '.$current_rfc7231_datetime);
        header('Content-Type: image/jpeg');

        // Return the generated image to the client
        $draw_text_on_template_helper = new DrawTextOnTemplateController();

        try {
            $draw_text_on_template_helper->draw_text_to_image($this->selected_banner_template, false, true, $request->ip());
        } catch (Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }

    /**
     * Redirect to the templates specified URL, if any is specified.
     */
    public function redirect_url()
    {
        if (! is_null($this->response_text)) {
            return response($this->response_text, $this->response_code);
        }

        if (is_null($this->selected_banner_template->redirect_url)) {
            return Redirect::route('api.banner', ['banner_id' => base_convert($this->banner->id, 10, 35)]);
        }

        return Redirect::to($this->selected_banner_template->redirect_url, 302);
    }
}
