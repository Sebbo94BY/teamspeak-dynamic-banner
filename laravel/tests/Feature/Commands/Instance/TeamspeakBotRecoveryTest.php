<?php

namespace Tests\Feature\Commands\Instance;

use App\Console\Commands\Instance\TeamspeakBot;
use App\Http\Controllers\Helpers\TeamSpeakVirtualserver;
use App\Jobs\RestartTeamSpeakBot;
use App\Models\Instance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\ServerQueryException;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\TransportException;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;
use Tests\TestCase;

class TeamspeakBotRecoveryTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_invalid_server_id_schedules_one_restart_immediately(): void
    {
        Carbon::setTestNow('2026-09-28 12:00:00');
        Queue::fake();
        $instance = Instance::factory()->create();
        $command = $this->command($instance);
        $helper = $this->failingHelper(new ServerQueryException('invalid serverID'));

        $this->assertFalse($command->reconnectForTest($helper));
        $this->assertFalse($command->reconnectForTest($helper));

        $instance->refresh();
        $this->assertSame(1, $instance->bot_recovery_attempts);
        $this->assertTrue($instance->bot_restart_scheduled_at->equalTo(now()->addSeconds(30)));
        $this->assertStringContainsString('invalid serverID', $instance->bot_restart_reason);
        $this->assertSame(1, $command->cleanupCount);
        Queue::assertPushed(RestartTeamSpeakBot::class, 1);
    }

    public function test_first_two_transient_reconnect_failures_keep_normal_retry_behavior(): void
    {
        Queue::fake();
        $command = $this->command(Instance::factory()->create());
        $helper = $this->failingHelper(new TransportException('Connection refused'));

        $this->assertFalse($command->reconnectForTest($helper));
        $this->assertFalse($command->reconnectForTest($helper));

        $this->assertSame(2, $command->retryCount);
        $this->assertSame(0, $command->cleanupCount);
        Queue::assertNothingPushed();
    }

    public function test_third_transient_reconnect_failure_schedules_recovery(): void
    {
        Carbon::setTestNow('2026-09-28 12:00:00');
        Queue::fake();
        $instance = Instance::factory()->create();
        $command = $this->command($instance);
        $helper = $this->failingHelper(new TransportException('Connection refused'));

        $command->reconnectForTest($helper);
        $command->reconnectForTest($helper);
        $this->assertFalse($command->reconnectForTest($helper));

        $instance->refresh();
        $this->assertSame(1, $instance->bot_recovery_attempts);
        $this->assertSame(2, $command->retryCount);
        $this->assertSame(1, $command->cleanupCount);
        Queue::assertPushed(RestartTeamSpeakBot::class, 1);
    }

    private function failingHelper(\Throwable $exception): TeamSpeakVirtualserver
    {
        return new class($exception) extends TeamSpeakVirtualserver
        {
            public function __construct(private \Throwable $exception)
            {
            }

            public function get_virtualserver_connection(bool $blocking = true): Server
            {
                throw $this->exception;
            }
        };
    }

    private function command(Instance $instance): TeamspeakBot
    {
        $command = new class extends TeamspeakBot
        {
            public int $retryCount = 0;

            public int $cleanupCount = 0;

            public function setInstanceForTest(Instance $instance): void
            {
                $this->instance = $instance;
            }

            public function reconnectForTest(TeamSpeakVirtualserver $helper): bool
            {
                return $this->reconnect_to_virtualserver($helper);
            }

            protected function wait_before_reconnect(): void
            {
                $this->retryCount++;
            }

            protected function cleanup_instance_process_id(): void
            {
                $this->cleanupCount++;
            }

            protected function message(string $log_level, string $message)
            {
            }

            public function __destruct()
            {
            }
        };
        $command->setInstanceForTest($instance);

        return $command;
    }
}
