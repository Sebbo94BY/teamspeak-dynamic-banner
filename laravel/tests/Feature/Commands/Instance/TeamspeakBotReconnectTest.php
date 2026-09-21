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

    private function command(): TeamspeakBot
    {
        $instance = new Instance();
        $instance->host = 'ts.sample.com';

        $command = new class extends TeamspeakBot
        {
            public int $refreshCount = 0;

            public int $retryCount = 0;

            public array $messages = [];

            public function setInstanceForTest(Instance $instance): void
            {
                $this->instance = $instance;
            }

            public function reconnectForTest(TeamSpeakVirtualserver $helper): bool
            {
                return $this->reconnect_to_virtualserver($helper);
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

            protected function message(string $log_level, string $message)
            {
                $this->messages[] = $log_level.': '.$message;
            }
        };
        $command->setInstanceForTest($instance);

        return $command;
    }
}
