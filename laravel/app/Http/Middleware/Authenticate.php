<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     */
    protected function redirectTo(Request $request)
    {
        if (! $request->expectsJson()) {
            if (User::all()->count() > 0) {
                return route('login');
            }

            return route('setup.installer.requirements');
        }
    }
}
