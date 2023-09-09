<?php

namespace App\Http\Controllers\Setup\Installer;

use App\Http\Controllers\Administration\SystemStatusController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\SystemStatusController as HelpersSystemStatusController;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;

class RequirementsController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show_view(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        if (User::all()->count() > 0) {
            return Redirect::route('dashboard');
        }

        $system_status_helper = new HelpersSystemStatusController();
        return view('setup.installer.requirements',$system_status_helper->system_status());
    }
}
