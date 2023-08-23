<?php

namespace Tests\Feature\Commands\Process;

use App\Models\Instance;
use App\Models\InstanceProcess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendPcntlSignalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test that no signal can be sent to a not existing process ID.
     */
    public function test_no_signal_can_be_sent_to_a_not_existing_process_id(): void
    {
        $non_existing_process_id = 1337;

        $this->artisan('process:send-signal', ['pcntl_signal' => SIGHUP, 'process_id' => $non_existing_process_id])
            ->expectsOutput('The process ID `'.$non_existing_process_id.'` does not exist anymore. I can not send any signal to it.')
            ->assertSuccessful();
    }
}
