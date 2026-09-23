<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ThrottledErrorLogger
{
    /**
     * Log an error only once during the given cooldown period.
     *
     * The file cache is intentional: the default cache store may be Redis,
     * which can itself be the source of a repeated error.
     */
    public function log(string $message, int $cooldown_in_seconds): void
    {
        $cache_key = 'throttled-error-log:'.hash('sha256', $message);

        if (Cache::store('file')->add($cache_key, true, now()->addSeconds($cooldown_in_seconds))) {
            Log::error($message);
        }
    }
}
