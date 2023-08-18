<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FontsController extends Controller
{
    /**
     * Callback function to only return TTF files.
     */
    public function is_ttf_file($value, $key)
    {
        return preg_match("/\.ttf$/", $value);
    }

    /**
     * Display the fonts view.
     */
    public function fonts(): View
    {
        $installed_ttf_files = array_filter(Storage::disk('public')->files('fonts/'), $this->is_ttf_file(...), ARRAY_FILTER_USE_BOTH);

        return view('administration.fonts', ['fonts' => $installed_ttf_files]);
    }
}
