<?php

namespace Tests\Feature\Jobs;

use App\Jobs\RestartTeamSpeakBot;
use App\Models\Instance;
use App\Models\InstanceProcess;
use App\Support\TeamSpeakBotLauncher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class RestartTeamSpeakBotTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Mockery::close();

        parent::tearDown();
    }

    public function test_matching_recovery_job_launches_bot_and_clears_plan(): void
    {
        Carbon::setTestNow('2026-09-28 12:00:00');
        $instance = Instance::factory()->create([
            'bot_restart_scheduled_at' => now(),
            'bot_restart_reason' => 'invalid serverID',
        ]);
        $launcher = Mockery::mock(TeamSpeakBotLauncher::class);
        $launcher->shouldReceive('start')->once()->with(Mockery::on(fn (Instance $actual) => $actual->is($instance)))->andReturnTrue();

        (new RestartTeamSpeakBot($instance->id, now()->getTimestamp()))->handle($launcher);

        $instance->refresh();
        $this->assertNull($instance->bot_restart_scheduled_at);
        $this->assertNull($instance->bot_restart_reason);
    }

    public function test_stale_recovery_job_does_not_launch_or_change_a_newer_plan(): void
    {
        Carbon::setTestNow('2026-09-28 12:00:00');
        $instance = Instance::factory()->create([
            'bot_restart_scheduled_at' => now()->addMinute(),
            'bot_restart_reason' => 'newer recovery',
        ]);
        $launcher = Mockery::mock(TeamSpeakBotLauncher::class);
        $launcher->shouldNotReceive('start');

        (new RestartTeamSpeakBot($instance->id, now()->getTimestamp()))->handle($launcher);

        $instance->refresh();
        $this->assertTrue($instance->bot_restart_scheduled_at->equalTo(now()->addMinute()));
        $this->assertSame('newer recovery', $instance->bot_restart_reason);
    }

    public function test_existing_manual_bot_prevents_delayed_job_from_starting_a_second_bot(): void
    {
        Carbon::setTestNow('2026-09-28 12:00:00');
        $instance = Instance::factory()->create([
            'bot_restart_scheduled_at' => now(),
            'bot_restart_reason' => 'recovery',
        ]);
        InstanceProcess::factory()->for($instance)->create();
        $launcher = Mockery::mock(TeamSpeakBotLauncher::class);
        $launcher->shouldNotReceive('start');

        (new RestartTeamSpeakBot($instance->id, now()->getTimestamp()))->handle($launcher);

        $instance->refresh();
        $this->assertNull($instance->bot_restart_scheduled_at);
        $this->assertNull($instance->bot_restart_reason);
    }
}
