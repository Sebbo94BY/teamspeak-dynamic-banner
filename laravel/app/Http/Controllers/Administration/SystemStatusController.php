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
        $system_status = $system_status_helper->system_status_json();

        return view('administration.systemstatus', [
            'system_status' => json_decode($system_status),
            'system_status_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $system_status),
            'system_status_danger_count' => preg_match_all("/\"severity\"\:\"danger\"/", $system_status),
        ]);
    }
}
