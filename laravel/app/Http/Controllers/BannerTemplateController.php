<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerTemplateAddRequest;
use App\Http\Requests\BannerTemplateAddTemplateRequest;
use App\Http\Requests\BannerTemplateDisableRequest;
use App\Http\Requests\BannerTemplateEditRequest;
use App\Http\Requests\BannerTemplateEnableRequest;
use App\Http\Requests\BannerTemplateRemoveRequest;
use App\Jobs\CloneOriginalTemplate;
use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class BannerTemplateController extends Controller
{
    /**
     * Properties / Settings
     */
    private string $upload_directory_drawed_grid_text = 'uploads/templates/drawed_grid_text';

    private string $upload_directory_drawed_text = 'uploads/templates/drawed_text';

    /**
     * Display the edit view.
     */
    public function edit(BannerTemplateEditRequest $request): View
    {
        $banner = Banner::find($request->banner_id);

        return view('banner.template', ['banner_id' => $banner->id])->with([
            'banner' => $banner,
            'templates' => Template::all(),
        ]);
    }

    /**
     * Display the view to add a template to the banner.
     */
    public function add_template(BannerTemplateAddTemplateRequest $request): View
    {
        //todo obsolete
        $banner = Banner::find($request->banner_id);

        return view('banner.template_add', ['banner_id' => $banner->id])->with([
            'banner' => $banner,
            'templates' => Template::all(),
        ]);
    }

    /**
     * Adds a template to a banner configuration.
     */
    public function add(BannerTemplateAddRequest $request): RedirectResponse
    {
        $banner_template = new BannerTemplate;
        $banner_template->banner_id = $request->banner_id;
        $banner_template->template_id = $request->template_id;

        if (! $banner_template->save()) {
            return Redirect::route('banner.template.add', ['banner_id' => $request->banner_id])->with([
                'error' => 'banner-template-add-error',
                'message' => 'Failed to save the new data set into the database. Please try again.',
            ]);
        }

        $banner_template->file_path_drawed_grid_text = $this->upload_directory_drawed_grid_text.'/'.$banner_template->id;
        $banner_template->file_path_drawed_text = $this->upload_directory_drawed_text.'/'.$banner_template->id;

        if (! $banner_template->save()) {
            return Redirect::route('banner.template.add', ['banner_id' => $request->banner_id])->with([
                'error' => 'banner-template-add-error',
                'message' => 'Failed to save the new data set into the database. Please try again.',
            ]);
        }

        // We need to dispatch these jobs synchronous as the view, which we will show afterwards
        // will otherwise show a not existing image as the jobs may have not been finished.
        CloneOriginalTemplate::dispatchSync($banner_template->template, $banner_template->file_path_drawed_grid_text, true);
        CloneOriginalTemplate::dispatchSync($banner_template->template, $banner_template->file_path_drawed_text);

        return Redirect::route('banner.template.configuration.edit', ['banner_template_id' => $banner_template->id])->with([
            'success' => 'banner-template-add-successful',
            'message' => 'Successfully added the template to the banner.',
        ]);
    }

    /**
     * Removes a template from a banner configuration.
     */
    public function remove(BannerTemplateRemoveRequest $request): RedirectResponse
    {
        $banner_template = BannerTemplate::find($request->banner_template_id);

        if (! $banner_template->delete()) {
            return Redirect::route('banner.templates', ['banner_id' => $banner_template->banner_id])->with([
                'error' => 'banner-template-remove-error',
                'message' => 'Failed to delete the data set from the database. Please try again.',
            ]);
        }

        return Redirect::route('banner.templates', ['banner_id' => $banner_template->banner_id])->with([
            'success' => 'banner-template-remove-successful',
            'message' => 'Successfully removed the template from the banner.',
        ]);
    }

    /**
     * Disables a template of a banner configuration.
     */
    public function disable(BannerTemplateDisableRequest $request): RedirectResponse
    {
        $banner_template = BannerTemplate::find($request->banner_template_id);

        $banner_template->enabled = false;

        if (! $banner_template->save()) {
            return Redirect::route('banner.templates', ['banner_id' => $banner_template->banner_id])->with([
                'error' => 'banner-template-disable-error',
                'message' => 'Failed to update the data set from the database. Please try again.',
            ]);
        }

        return Redirect::route('banner.templates', ['banner_id' => $banner_template->banner_id])->with([
            'success' => 'banner-template-disable-successful',
            'message' => 'Successfully disabled the template of the banner.',
        ]);
    }

    /**
     * Enables a template of a banner configuration.
     */
    public function enable(BannerTemplateEnableRequest $request): RedirectResponse
    {
        $banner_template = BannerTemplate::findOrFail($request->banner_template_id);

        $banner_template->enabled = true;

        if (! $banner_template->save()) {
            return Redirect::route('banner.templates', ['banner_id' => $banner_template->banner_id])->with([
                'error' => 'banner-template-enable-error',
                'message' => 'Failed to update the data set from the database. Please try again.',
            ]);
        }

        return Redirect::route('banner.templates', ['banner_id' => $banner_template->banner_id])->with([
            'success' => 'banner-template-enable-successful',
            'message' => 'Successfully enabled the template of the banner.',
        ]);
    }
}
