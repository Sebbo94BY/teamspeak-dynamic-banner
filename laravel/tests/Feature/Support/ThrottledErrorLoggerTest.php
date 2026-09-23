<?php

namespace Tests\Feature\Support;

use App\Support\ThrottledErrorLogger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ThrottledErrorLoggerTest extends TestCase
{
    public function test_it_logs_an_error_only_once_during_the_cooldown(): void
    {
        Cache::store('file')->clear();
        Log::spy();

        $logger = new ThrottledErrorLogger;
        $logger->log('Matomo tracking failed.', 300);
        $logger->log('Matomo tracking failed.', 300);

        Log::shouldHaveReceived('error')->once()->with('Matomo tracking failed.');
    }

    public function test_it_does_not_suppress_a_different_error(): void
    {
        Cache::store('file')->clear();
        Log::spy();

        $logger = new ThrottledErrorLogger;
        $logger->log('First error.', 300);
        $logger->log('Second error.', 300);

        Log::shouldHaveReceived('error')->twice();
    }
}
