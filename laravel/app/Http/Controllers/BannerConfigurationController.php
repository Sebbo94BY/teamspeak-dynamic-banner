<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\DrawTextOnTemplateController;
use App\Http\Requests\BannerConfigurationDeleteRequest;
use App\Http\Requests\BannerConfigurationEditRequest;
use App\Http\Requests\BannerConfigurationUpsertRequest;
use App\Models\BannerConfiguration;
use App\Models\BannerTemplate;
use App\Models\Font;
use App\Models\Instance;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class BannerConfigurationController extends Controller
{
    /**
     * Display the edit view.
     */
    public function edit(BannerConfigurationEditRequest $request): View|RedirectResponse
    {
        return view('banner.configuration')->with([
            'banner_template' => BannerTemplate::find($request->banner_template_id),
            'fonts' => Font::all(),
            'instance' => Instance::all(),
        ]);
    }

    /**
     * Upserts the banner template configuration.
     */
    public function upsert(BannerConfigurationUpsertRequest $request): View|RedirectResponse
    {
        $banner_template = BannerTemplate::find($request->banner_template_id);
        $banner_template->name = $request->name;
        $banner_template->redirect_url = $request->redirect_url;
        $banner_template->disable_at = (is_null($request->disable_at)) ? null : Carbon::parse($request->disable_at, $request->header('X-Timezone'))->setTimezone(config('app.timezone'));
        $banner_template->time_based_enable_at = (is_null($request->time_based_enable_at)) ? null : Carbon::parse($request->time_based_enable_at, $request->header('X-Timezone'))->setTimezone(config('app.timezone'));
        $banner_template->time_based_disable_at = (is_null($request->time_based_disable_at)) ? null : Carbon::parse($request->time_based_disable_at, $request->header('X-Timezone'))->setTimezone(config('app.timezone'));

        if (! $banner_template->save()) {
            return Redirect::route('banner.template.configuration.edit', ['banner_id' => $banner_template->banner_id, 'template_id' => $banner_template->template_id])
                ->withInput($request->all())
                ->with([
                    'banner_template' => $banner_template,
                    'error' => 'banner-template-redirect-url-error',
                    'message' => 'Failed to update the redirect URL for this banner template in the database. Please try again.',
                ]);
        }

        $banner_configurations = [];

        for ($i = 0; $i < count($request->validated('configuration')['text']); $i++) {
            if (isset($configuration)) {
                unset($configuration);
            }

            $configuration['id'] = isset($request->validated('configuration')['banner_configuration_id'][$i]) ? $request->validated('configuration')['banner_configuration_id'][$i] : null;
            $configuration['banner_template_id'] = $request->validated('banner_template_id');
            $configuration['x_coordinate'] = $request->validated('configuration')['x_coordinate'][$i];
            $configuration['y_coordinate'] = $request->validated('configuration')['y_coordinate'][$i];
            $configuration['text'] = $request->validated('configuration')['text'][$i];
            $configuration['font_id'] = $request->validated('configuration')['font_id'][$i];
            $configuration['font_size'] = $request->validated('configuration')['font_size'][$i];
            $configuration['font_angle'] = $request->validated('configuration')['font_angle'][$i];
            $configuration['font_color_in_hexadecimal'] = $request->validated('configuration')['font_color_in_hexadecimal'][$i];

            $banner_configurations[] = $configuration;
        }

        if (! BannerConfiguration::upsert($banner_configurations, ['id'], [
            'banner_template_id',
            'x_coordinate',
            'y_coordinate',
            'text',
            'font_id',
            'font_size',
            'font_angle',
            'font_color_in_hexadecimal',
        ])) {
            return Redirect::route('banner.template.configuration.edit', ['banner_template_id' => $banner_template->id])
                ->withInput($request->all())
                ->with([
                    'banner_template' => $banner_template,
                    'error' => 'banner-template-upsert-error',
                    'message' => 'Failed to add or update the data set in the database. Please try again.',
                ]);
        }

        $draw_text_on_template_helper = new DrawTextOnTemplateController();

        try {
            $draw_text_on_template_helper->draw_text_to_image($banner_template, true, false, $request->ip());
        } catch (Exception $exception) {
            return Redirect::route('banner.template.configuration.edit', ['banner_template_id' => $banner_template->id])
                ->with([
                    'banner_template' => $banner_template,
                    'error' => 'banner-template-draw-text-to-image-error',
                    'message' => "Failed to draw the text to the template: $exception",
                ]);
        }

        return Redirect::route('banner.template.configuration.edit', ['banner_template_id' => $banner_template->id])
            ->with([
                'banner_template' => $banner_template,
                'success' => 'banner-template-upsert-success',
                'message' => 'Successfully added or updated the data set in the database.',
            ]);
    }

    /**
     * Deletes a single banner configuration.
     */
    public function delete(BannerConfigurationDeleteRequest $request): RedirectResponse
    {
        $banner_configuration = BannerConfiguration::find($request->banner_configuration_id);

        if (! $banner_configuration->delete()) {
            return Redirect::route('banners')->with([
                'error' => 'banner-configuration-delete-error',
                'message' => 'Failed to delete the banner configuration from the database. Please try again.',
            ]);
        }

        $draw_text_on_template_helper = new DrawTextOnTemplateController();

        try {
            $draw_text_on_template_helper->draw_text_to_image($banner_configuration->bannerTemplate, true, false, $request->ip());
        } catch (Exception $exception) {
            return Redirect::route('banner.template.configuration.edit', ['banner_template_id' => $banner_configuration->bannerTemplate->id])
                ->with([
                    'banner_template' => $banner_configuration->bannerTemplate,
                    'error' => 'banner-template-draw-text-to-image-error',
                    'message' => "Failed to draw the text to the template: $exception",
                ]);
        }

        return Redirect::route('banner.template.configuration.edit', ['banner_template_id' => $banner_configuration->bannerTemplate->id])
            ->with([
                'banner_template' => $banner_configuration->bannerTemplate,
                'success' => 'banner-configuration-delete-successful',
                'message' => 'Successfully deleted the banner configuration.',
            ]);
    }
}
