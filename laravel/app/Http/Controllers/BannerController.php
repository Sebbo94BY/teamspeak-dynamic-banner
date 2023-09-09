<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerAddRequest;
use App\Http\Requests\BannerDeleteRequest;
use App\Http\Requests\BannerEditRequest;
use App\Http\Requests\BannerUpdateRequest;
use App\Models\Banner;
use App\Models\Instance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class BannerController extends Controller
{
    /**
     * Display the main page.
     */
    public function overview(): View
    {
        return view('banners')->with([
            'banners'=>Banner::all(),
            'instance_list'=>Instance::all(),
        ]);
    }

    /**
     * Save a new data set.
     */
    public function save(BannerAddRequest $request): RedirectResponse
    {
        $banner = new Banner;
        $banner->name = $request->name;
        $banner->instance_id = $request->instance_id;
        $banner->random_rotation = $request->has('random_rotation');

        if (! $banner->save()) {
            return Redirect::route('banners')->withInput($request->all())->with([
                'error' => 'banner-add-error',
                'message' => 'Failed to save the new data set into the database. Please try again.',
            ]);
        }

        return Redirect::route('banners')->with([
            'success' => 'banner-add-successful',
            'message' => 'Successfully added the new banner.',
        ]);
    }

    /**
     * Display the edit view.
     */
    public function edit(BannerEditRequest $request): View|RedirectResponse
    {
        Banner::find($request->banner_id);

        return redirect()->route('banners');
    }

    /**
     * Update an existing data set.
     */
    public function update(BannerUpdateRequest $request): RedirectResponse
    {
        $banner = Banner::find($request->banner_id);

        $banner->name = $request->name;
        $banner->instance_id = $request->instance_id;
        $banner->random_rotation = ($request->has('random_rotation')) ? true : false;

        if (! $banner->save()) {
            return Redirect::route('banners')
                ->withInput($request->all())
                ->with([
                    'error' => 'banner-edit-error',
                    'message' => 'Failed to update the database entry. Please try again.',
                ]);
        }

        return Redirect::route('banners')->with([
            'success' => 'banner-edit-successful',
            'message' => 'Successfully updated the banner.',
        ]);
    }

    /**
     * Deletes a specific banner.
     */
    public function delete(BannerDeleteRequest $request): RedirectResponse
    {
        $banner = Banner::find($request->banner_id);

        if (! $banner->delete()) {
            return Redirect::route('banners')->with([
                'error' => 'banner-delete-error',
                'message' => 'Failed to delete the banner from the database. Please try again.',
            ]);
        }

        return Redirect::route('banners')->with([
            'success' => 'banner-delete-successful',
            'message' => 'Successfully deleted the banner.',
        ]);
    }
}
