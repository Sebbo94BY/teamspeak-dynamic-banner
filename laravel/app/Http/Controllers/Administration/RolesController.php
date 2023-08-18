<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    /**
     * Display the roles view.
     */
    public function roles(): View
    {
        return view('administration.roles', ['roles' => Role::all()]);
    }
}
