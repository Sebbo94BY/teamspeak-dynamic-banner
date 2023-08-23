<?php

namespace Tests\Feature\Commands\Process;

use App\Models\Instance;
use App\Models\InstanceProcess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CleanupDeadPidsTest extends TestCase
{
    use RefreshDatabase;

    protected InstanceProcess $instance_process;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->instance_process = InstanceProcess::factory()->for(
            Instance::factory()->create()
        )->create();
    }

    /**
     * Test that the process gets cleaned up when the process ID is not active anymore.
     */
    public function test_process_gets_cleaned_up_when_the_pid_is_not_active_anymore(): void
    {
        $this->artisan('process:cleanup-dead-pids')
            ->expectsOutput('PID '.$this->instance_process->process_id.' is not active anymore. Successfully removed it from the database.')
            ->assertSuccessful();
    }
}
