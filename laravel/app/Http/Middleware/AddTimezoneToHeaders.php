<?php

namespace App\Http\Middleware;

use Closure;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AddTimezoneToHeaders
{
    /**
     * The timezone header key in HTTP requests.
     */
    protected string $timezone_header_key = 'X-Timezone';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the HTTP request has already the required header, leave it as it is
        if ($request->hasHeader($this->timezone_header_key)) {
            return $next($request);
        }

        // If the user has set a preferred timezone in the user profile, use this timezone
        if (Auth::check() and ! is_null(Auth::user()->timezone)) {
            $request->headers->set($this->timezone_header_key, Auth::user()->timezone, true);

            return $next($request);
        }

        // Set applications timezone as default timezone for clients
        $request->headers->set($this->timezone_header_key, config('app.timezone'));

        $client_preferred_language = $request->getPreferredLanguage();

        if (! is_null($client_preferred_language)) {
            // en_US => US
            $country_code = substr($client_preferred_language, -2);

            // Returns for example ['Europe/Berlin', 'Europe/Busingen']
            $timezone = timezone_identifiers_list(DateTimeZone::PER_COUNTRY, $country_code);

            // Some clients like the Microsoft Edge browser may only send 'en' as preferred language,
            // so lets try to determine the timezone based on this value as well if the above detection
            // did not find any possible timezone.
            if (count($timezone) == 0) {
                // en_US => en
                $locale = substr($client_preferred_language, 0, 2);

                $timezone = timezone_identifiers_list(DateTimeZone::PER_COUNTRY, strtoupper($locale));
            }

            // If we have found one or more timezones, set the first respectively as HTTP header
            if (count($timezone) >= 1) {
                $request->headers->set($this->timezone_header_key, reset($timezone), true);
            }
        }

        return $next($request);
    }
}
