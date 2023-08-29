<?php

namespace Tests\Feature\Commands\Process;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
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
        $process = Process::start('sleep 3');

        $process_id = $process->id();

        while ($process->running()) {
            sleep(1);
        }

        $this->assertFalse($process->running());
        $this->artisan('process:send-signal', ['pcntl_signal' => SIGHUP, 'process_id' => $process_id])
            ->expectsOutput('The process ID `'.$process_id.'` does not exist anymore. I can not send any signal to it.')
            ->assertSuccessful();
    }

    /**
     * Test that a signal can be sent to an existing process ID.
     */
    public function test_a_signal_can_be_sent_to_an_existing_process_id(): void
    {
        $process = Process::start('sleep 10');
        $process_id = $process->id();

        $this->assertTrue($process->running());
        $this->artisan('process:send-signal', ['pcntl_signal' => SIGHUP, 'process_id' => $process_id])
            ->expectsOutput('Sending the PCNLT signal `1` to the process ID `'.$process_id.'`...')
            ->expectsOutput('The signal was successful.')
            ->assertSuccessful();

        $other_process = Process::start('sleep 10');
        $other_process_id = $other_process->id();

        $this->assertTrue($other_process->running());
        $this->artisan('process:send-signal', ['pcntl_signal' => 1, 'process_id' => $other_process_id])
            ->expectsOutput('Sending the PCNLT signal `1` to the process ID `'.$other_process_id.'`...')
            ->expectsOutput('The signal was successful.')
            ->assertSuccessful();
    }
}
