<?php

namespace Tests\Feature\Support;

use App\Models\Instance;
use App\Support\InstanceHealthCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class InstanceHealthCheckStateTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_a_successful_standalone_check_does_not_resolve_a_bot_runtime_failure(): void
    {
        Carbon::setTestNow('2026-09-28 12:00:00');
        $instance = Instance::factory()->create();
        $health = app(InstanceHealthCheck::class);

        $health->recordRuntimeFailure($instance, 'insufficient client permissions (failed on serverinfo 12/0xC)');
        $health->storeResult($instance->fresh(), [
            'healthy' => true,
            'checks' => [],
            'channel_list' => [],
        ]);

        $instance->refresh();

        $this->assertTrue($instance->health_last_check_healthy);
        $this->assertNotNull($instance->health_last_runtime_error_at);
        $this->assertNull($instance->health_last_success_at);
        $this->assertStringContainsString('b_virtualserver_info_view', $instance->health_last_error);
        $this->assertNotNull($instance->health_problem_started_at);
    }

    public function test_a_complete_bot_refresh_resolves_runtime_failure_and_resets_recovery_state(): void
    {
        Carbon::setTestNow('2026-09-28 12:00:00');
        $instance = Instance::factory()->create([
            'bot_recovery_attempts' => 3,
            'bot_restart_scheduled_at' => now()->addMinutes(5),
            'bot_restart_reason' => 'invalid serverID',
        ]);
        $health = app(InstanceHealthCheck::class);
        $health->recordRuntimeFailure($instance, 'Connection lost');

        Carbon::setTestNow('2026-09-28 12:01:00');
        $health->recordRuntimeSuccess($instance->fresh());

        $instance->refresh();

        $this->assertTrue($instance->health_last_success_at->greaterThan($instance->health_last_runtime_error_at));
        $this->assertNull($instance->health_last_check_healthy);
        $this->assertSame(0, $instance->bot_recovery_attempts);
        $this->assertNull($instance->bot_restart_scheduled_at);
        $this->assertNull($instance->bot_restart_reason);
        $this->assertNull($instance->health_problem_started_at);
    }

    public function test_health_state_updates_do_not_change_the_instance_configuration_timestamp(): void
    {
        Carbon::setTestNow('2026-09-28 12:00:00');
        $instance = Instance::factory()->create();
        $configurationUpdatedAt = $instance->updated_at;

        Carbon::setTestNow('2026-09-28 12:05:00');
        app(InstanceHealthCheck::class)->storeResult($instance, [
            'healthy' => true,
            'checks' => [],
            'channel_list' => [],
        ]);

        $instance->refresh();

        $this->assertTrue($instance->updated_at->equalTo($configurationUpdatedAt));
        $this->assertTrue($instance->health_last_checked_at->equalTo(now()));
    }
}
