<?php

namespace Tests\Feature\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class KernelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test, that the scheduling timezone is UTC.
     */
    public function test_scheduling_timezone_is_utc(): void
    {
        $this->assertEquals('UTC', App::make(Schedule::class)->events()[0]->timezone);
    }
}
