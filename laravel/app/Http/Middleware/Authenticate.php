<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(\Illuminate\Http\Request $request)
    {
        if (! $request->expectsJson()) {
            if (User::all()->count() > 0) {
                return route('login');
            }

            return route('setup.installer.requirements');
        }
    }
}
