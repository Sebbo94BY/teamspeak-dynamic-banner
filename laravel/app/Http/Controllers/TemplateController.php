<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateAddRequest;
use App\Http\Requests\TemplateDeleteRequest;
use App\Http\Requests\TemplateUpdateRequest;
use App\Jobs\DrawGridSystemOnTemplate;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TemplateController extends Controller
{
    /**
     * Properties / Settings
     */
    private string $upload_directory_original = 'uploads/templates';

    private string $upload_directory_drawed_grid = 'uploads/templates/drawed_grid';

    /**
     * Display the main page.
     */
    public function overview(): View
    {
        return view('templates')->with('templates', Template::all());
    }

    /**
     * Save a new data set.
     */
    public function save(TemplateAddRequest $request): RedirectResponse
    {
        $filename = time().'_'.$request->file->getClientOriginalName();
        $request->file->move(public_path($this->upload_directory_original), $filename);

        list($width, $height) = getimagesize($this->upload_directory_original.'/'.$filename);

        $template = new Template;
        $template->alias = $request->alias;
        $template->filename = $filename;
        $template->file_path_original = $this->upload_directory_original;
        $template->file_path_drawed_grid = $this->upload_directory_drawed_grid;
        $template->width = $width;
        $template->height = $height;

        if (! $template->save()) {
            return Redirect::route('templates')->withInput($request->all())->with([
                'error' => 'template-add-error',
                'message' => 'Failed to save the new template in the database. Please try again.',
            ]);
        }

        DrawGridSystemOnTemplate::dispatch($template);

        return Redirect::route('templates')->with([
            'success' => 'template-add-successful',
            'message' => 'Successfully added the new template.',
        ]);
    }

    /**
     * Update an existing data set.
     */
    public function update(TemplateUpdateRequest $request): RedirectResponse
    {
        $template = Template::find($request->template_id);

        $template->alias = $request->alias;

        if ($request->file) {
            $file_path_original = public_path($template->file_path_original).'/'.$template->filename;
            if (file_exists($file_path_original)) {
                unlink($file_path_original);
            }

            $upload_directory_drawed_grid = public_path($template->upload_directory_drawed_grid).'/'.$template->filename;
            if (file_exists($upload_directory_drawed_grid)) {
                unlink($upload_directory_drawed_grid);
            }

            $filename = time().'_'.$request->file->getClientOriginalName();
            $request->file->move(public_path($this->upload_directory_original), $filename);

            list($width, $height) = getimagesize($this->upload_directory_original.'/'.$filename);

            $template->filename = $filename;
            $template->file_path_original = $this->upload_directory_original;
            $template->file_path_drawed_grid = $this->upload_directory_drawed_grid;
            $template->width = $width;
            $template->height = $height;

            DrawGridSystemOnTemplate::dispatch($template);
        }

        if (! $template->save()) {
            return Redirect::route('template.edit', ['template_id' => $template->id])
                ->withInput($request->all())
                ->with([
                    'error' => 'template-edit-error',
                    'message' => 'Failed to update the database entry. Please try again.',
                ]);
        }

        return Redirect::route('templates')->with([
            'success' => 'template-edit-successful',
            'message' => 'Successfully updated the template.',
        ]);
    }

    /**
     * Delete the template.
     */
    public function delete(TemplateDeleteRequest $request): RedirectResponse
    {
        $template = Template::find($request->template_id);

        $file_path_original = public_path($template->file_path_original).'/'.$template->filename;
        if (file_exists($file_path_original)) {
            unlink($file_path_original);
        }

        $upload_directory_drawed_grid = public_path($template->upload_directory_drawed_grid).'/'.$template->filename;
        if (file_exists($upload_directory_drawed_grid)) {
            unlink($upload_directory_drawed_grid);
        }

        foreach ($template->banner_templates() as $banner_template) {
            $file_path_drawed_grid_text = public_path($banner_template->file_path_drawed_grid_text).'/'.$template->filename;
            if (file_exists($file_path_drawed_grid_text)) {
                unlink($file_path_drawed_grid_text);
            }

            $file_path_drawed_text = public_path($banner_template->file_path_drawed_text).'/'.$template->filename;
            if (file_exists($file_path_drawed_text)) {
                unlink($file_path_drawed_text);
            }
        }

        if (! $template->delete()) {
            return redirect()->back()->with([
                'error' => 'template-delete-error',
                'message' => 'Failed to delete the template from the database. Please try again.',
            ]);
        }

        return Redirect::route('templates')->with([
            'success' => 'template-delete-successful',
            'message' => 'Successfully deleted the template.',
        ]);
    }
}
