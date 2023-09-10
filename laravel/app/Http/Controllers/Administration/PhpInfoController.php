<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PhpInfoController extends Controller
{
    /**
     * Display PHP information.
     */
    public function php_info(): View
    {
        ob_start();
        phpinfo();
        $phpinfo_full_html = ob_get_contents();
        ob_end_clean();
        $phpInfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpInfo);

        return view('administration.phpinfo')->with(['phpinfo'=>$phpInfo]);
    }
}
