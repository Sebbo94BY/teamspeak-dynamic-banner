<?php

namespace App\Http\Controllers\Setup\Installer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\SystemStatusController;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class RequirementsController extends Controller
{
    /**
     * Display the view
     */
    public function show_view(): View|Application|Factory|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        if (User::all()->count() > 0) {
            return Redirect::route('dashboard');
        }

        $system_status_helper = new SystemStatusController();

        return view('setup.installer.requirements', $system_status_helper->system_status());
    }
}
