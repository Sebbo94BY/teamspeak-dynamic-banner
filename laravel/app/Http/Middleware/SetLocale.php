<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            app()->setLocale(Auth::user()->localization->locale);
        } elseif (null !== $request->route()->parameter('locale')) {
            app()->setLocale($request->route()->parameter('locale'));
        } else {
            app()->setLocale(config('app.fallback_locale'));
        }

        return $next($request);
    }
}
