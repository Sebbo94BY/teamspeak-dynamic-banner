<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\SystemStatusController as HelpersSystemStatusController;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemStatusController extends Controller
{
    /**
     * Display system status information.
     */
    public function system_status(Request $request): View
    {
        $system_status_helper = new HelpersSystemStatusController;
        $queue_history_range = $request->query('queue_history_range', '30m');

        if (! array_key_exists($queue_history_range, HelpersSystemStatusController::QUEUE_METRIC_HISTORY_RANGES)) {
            $queue_history_range = '30m';
        }

        return view('administration.systemstatus', $system_status_helper->system_status($queue_history_range));
    }
}
