<?php

namespace Tests\Feature\Commands\Instance;

use App\Console\Commands\Instance\TeamspeakBot;
use App\Http\Controllers\Helpers\TeamSpeakVirtualserver;
use App\Models\Instance;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\TransportException;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;
use Tests\TestCase;

class TeamspeakBotReconnectTest extends TestCase
{
    public function test_initial_connection_retries_after_a_transient_failure(): void
    {
        $server = new class extends Server
        {
            public function __construct()
            {
            }
        };
        $helper = new class($server) extends TeamSpeakVirtualserver
        {
            public int $connections = 0;

            public function __construct(private Server $server)
            {
            }

            public function get_virtualserver_connection(bool $blocking = true): Server
            {
                $this->connections++;
                if ($this->connections === 1) {
                    throw new \Exception('Connection is still closing');
                }

                return $this->server;
            }
        };
        $command = $this->command();

        $this->assertTrue($command->connectForTest($helper));
        $this->assertSame(2, $helper->connections);
        $this->assertSame(1, $command->initialConnectionRetryCount);
    }

    public function test_initial_connection_stops_after_the_configured_attempts(): void
    {
        $helper = new class extends TeamSpeakVirtualserver
        {
            public int $connections = 0;

            public function __construct()
            {
            }

            public function get_virtualserver_connection(bool $blocking = true): Server
            {
                $this->connections++;

                throw new \Exception('Connection is still closing');
            }
        };
        $command = $this->command();

        $this->assertFalse($command->connectForTest($helper));
        $this->assertSame(3, $helper->connections);
        $this->assertSame(2, $command->initialConnectionRetryCount);
    }

    public function test_reconnect_refreshes_cached_data_and_registers_for_events(): void
    {
        $server = new class extends Server
        {
            public array $registeredEvents = [];

            public function __construct()
            {
            }

            public function notifyRegister(string $event, int $id = 0): void
            {
                $this->registeredEvents[] = [$event, $id];
            }
        };
        $helper = new class($server) extends TeamSpeakVirtualserver
        {
            public function __construct(private Server $server)
            {
            }

            public function get_virtualserver_connection(bool $blocking = true): Server
            {
                return $this->server;
            }
        };
        $command = $this->command();

        $this->assertTrue($command->reconnectForTest($helper));
        $this->assertSame(1, $command->refreshCount);
        $this->assertSame([['server', 0]], $server->registeredEvents);
        $this->assertSame(0, $command->retryCount);
    }

    public function test_failed_reconnect_waits_before_the_next_attempt(): void
    {
        $helper = new class extends TeamSpeakVirtualserver
        {
            public function __construct()
            {
            }

            public function get_virtualserver_connection(bool $blocking = true): Server
            {
                throw new TransportException('Connection refused');
            }
        };
        $command = $this->command();

        $this->assertFalse($command->reconnectForTest($helper));
        $this->assertSame(0, $command->refreshCount);
        $this->assertSame(1, $command->retryCount);
        $this->assertSame(['ERROR: Reconnect to `ts.sample.com` failed: Connection refused'], $command->messages);
    }

    public function test_stopping_bot_does_not_reconnect_after_a_transport_failure(): void
    {
        $helper = new class extends TeamSpeakVirtualserver
        {
            public int $connections = 0;

            public function __construct()
            {
            }

            public function get_virtualserver_connection(bool $blocking = true): Server
            {
                $this->connections++;

                throw new \Exception('This connection attempt must not happen');
            }
        };
        $command = $this->command();
        $command->stopForTest();

        $this->assertFalse($command->reconnectForTest($helper));
        $this->assertSame(0, $helper->connections);
        $this->assertSame(0, $command->retryCount);
    }

    private function command(): TeamspeakBot
    {
        $instance = new Instance();
        $instance->host = 'ts.sample.com';

        $command = new class extends TeamspeakBot
        {
            public int $refreshCount = 0;

            public int $retryCount = 0;

            public int $initialConnectionRetryCount = 0;

            public array $messages = [];

            public function setInstanceForTest(Instance $instance): void
            {
                $this->instance = $instance;
            }

            public function reconnectForTest(TeamSpeakVirtualserver $helper): bool
            {
                return $this->reconnect_to_virtualserver($helper);
            }

            public function connectForTest(TeamSpeakVirtualserver $helper): bool
            {
                return $this->connect_to_virtualserver_with_retries($helper);
            }

            public function stopForTest(): void
            {
                $this->keep_running = false;
            }

            public function __destruct()
            {
            }

            protected function refresh_cached_data(): void
            {
                $this->refreshCount++;
            }

            protected function wait_before_reconnect(): void
            {
                $this->retryCount++;
            }

            protected function wait_before_initial_connection_retry(): void
            {
                $this->initialConnectionRetryCount++;
            }

            protected function message(string $log_level, string $message)
            {
                $this->messages[] = $log_level.': '.$message;
            }
        };
        $command->setInstanceForTest($instance);

        return $command;
    }
}
