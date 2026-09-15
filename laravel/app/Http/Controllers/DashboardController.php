<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Instance;
use App\Models\Template;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     */
    public function dashboard(): View
    {
        return view('dashboard')->with([
            'instances_count' => Instance::count(),
            'templates_count' => Template::count(),
            'banners_count' => Banner::count(),
        ]);
    }
}
