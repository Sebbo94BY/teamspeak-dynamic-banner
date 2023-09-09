<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\SystemStatusController as HelpersSystemStatusController;
use Illuminate\View\View;

class SystemStatusController extends Controller
{
    /**
     * Display system status information.
     */
    public function system_status(): View
    {
        $system_status_helper = new HelpersSystemStatusController();
        return view('administration.systemstatus',$system_status_helper->system_status());
    }
}
