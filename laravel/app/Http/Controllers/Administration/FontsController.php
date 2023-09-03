<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\FontAddRequest;
use App\Http\Requests\FontDeleteRequest;
use App\Http\Requests\FontEditRequest;
use App\Http\Requests\FontUpdateRequest;
use App\Models\Font;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class FontsController extends Controller
{
    /**
     * Properties / Settings
     */
    protected string $upload_directory = 'uploads/fonts';

    /**
     * Display the fonts view.
     */
    public function fonts(): View
    {
        return view('administration.fonts', ['fonts' => Font::all()]);
    }

    /**
     * Display the font add form.
     */
    public function add_form(): View
    {
        return view('administration.font.add');
    }

    /**
     * Saves a new font.
     */
    public function create(FontAddRequest $request): RedirectResponse
    {
        $font = new Font;
        $font->filename = $request->validated('filename');

        $request->file->move(public_path($this->upload_directory), $font->filename);

        if (! $font->save()) {
            return Redirect::route('administration.font.add')->withInput($request->all())->with([
                'error' => 'font-add-error',
                'message' => 'Failed to save the new data set into the database. Please try again.',
            ]);
        }

        return Redirect::route('administration.fonts')->with([
            'success' => 'font-add-successful',
            'message' => 'Successfully added the new font.',
        ]);
    }

    /**
     * Display the font edit form.
     */
    public function edit_form(FontEditRequest $request): View
    {
        return view('administration.font.edit', ['font' => Font::find($request->font_id)]);
    }

    /**
     * Updates an existing font.
     */
    public function update(FontUpdateRequest $request): RedirectResponse
    {
        $font = Font::find($request->font_id);

        unlink(public_path($this->upload_directory).DIRECTORY_SEPARATOR.$font->filename);

        $font->filename = $request->validated('filename');

        // Uploading an updated version of the same file, which has the same filename uploads the file respectively, but Laravel
        // does not update the `updated_at` timestamp, so we force this update here explicitly.
        $font->updated_at = Carbon::now();

        $request->file->move(public_path($this->upload_directory), $font->filename);

        if (! $font->save(['timestamps' => false])) {
            return Redirect::route('administration.font.edit', ['font_id' => $request->font_id])
                ->withInput($request->all())
                ->with([
                    'error' => 'font-update-error',
                    'message' => 'Failed to update the font in the database. Please try again.',
                ]);
        }

        return Redirect::route('administration.fonts')->with([
            'success' => 'font-update-successful',
            'message' => 'Successfully updated the font.',
        ]);
    }

    /**
     * Deletes an existing font.
     */
    public function delete(FontDeleteRequest $request): RedirectResponse
    {
        $font = Font::find($request->font_id);

        unlink(public_path($this->upload_directory).DIRECTORY_SEPARATOR.$font->filename);

        if (! $font->delete()) {
            return Redirect::route('administration.fonts')->with([
                'error' => 'font-delete-error',
                'message' => 'Failed to delete the font from the database. Please try again.',
            ]);
        }

        return Redirect::route('administration.fonts')->with([
            'success' => 'font-delete-successful',
            'message' => 'Successfully deleted the font.',
        ]);
    }
}
