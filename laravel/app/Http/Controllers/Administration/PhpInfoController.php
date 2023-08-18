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
        return view('administration.phpinfo');
    }
}
