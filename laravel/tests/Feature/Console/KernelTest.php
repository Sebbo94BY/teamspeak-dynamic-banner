<?php

namespace Tests\Feature\Console;

use App\Models\Instance;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Process;
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

    public function test_autostart_skips_instances_with_a_planned_recovery(): void
    {
        $scheduledRecovery = Instance::factory()->create([
            'autostart_enabled' => true,
            'bot_restart_scheduled_at' => now()->addMinute(),
        ]);
        $eligible = Instance::factory()->create(['autostart_enabled' => true]);
        Process::fake();

        $this->autostartEvent()->run(app());

        Process::assertRan(fn ($process) => str_contains($process->command, 'instance:start-teamspeak-bot '.$eligible->id));
        Process::assertNotRan(fn ($process) => str_contains($process->command, 'instance:start-teamspeak-bot '.$scheduledRecovery->id));
    }

    private function autostartEvent(): mixed
    {
        return collect(App::make(Schedule::class)->events())
            ->first(fn ($event) => $event->getSummaryForDisplay() === 'instances:autostart');
    }
}
