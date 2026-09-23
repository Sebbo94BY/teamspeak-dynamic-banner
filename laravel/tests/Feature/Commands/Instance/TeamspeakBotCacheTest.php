<?php

namespace Tests\Feature\Commands\Instance;

use App\Console\Commands\Instance\TeamspeakBot;
use App\Models\Instance;
use Illuminate\Support\Facades\Redis;
use PlanetTeamSpeak\TeamSpeak3Framework\Helper\StringHelper;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Client;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;
use Tests\TestCase;

class TeamspeakBotCacheTest extends TestCase
{
    public function test_caches_each_connected_client_in_a_separate_hash_and_indexes_its_ip(): void
    {
        $instance = new Instance();
        $instance->id = 1;
        $server = new class extends Server
        {
            public array $clients = [];

            public function __construct()
            {
            }

            public function clientListReset(): void
            {
            }

            public function clientList(array $filter = []): array
            {
                return $this->clients;
            }
        };
        $server->clients = [
            $this->client($server, 42, 'Max', '192.168.2.17'),
            $this->client($server, 43, 'Peter', '192.168.2.20'),
            $this->client($server, 44, 'Ulrike', '192.168.2.34'),
        ];

        $command = new class extends TeamspeakBot
        {
            public function setContextForTest(Instance $instance, Server $server): void
            {
                $this->instance = $instance;
                $this->virtualserver = $server;
            }

            public function __destruct()
            {
            }

            protected function message(string $log_level, string $message)
            {
            }
        };
        $command->setContextForTest($instance, $server);

        foreach ([
            42 => ['Max', '192.168.2.17'],
            43 => ['Peter', '192.168.2.20'],
            44 => ['Ulrike', '192.168.2.34'],
        ] as $databaseId => [$nickname, $ip]) {
            Redis::shouldReceive('hmset')->once()->with(
                'instance_1_client_'.$databaseId,
                \Mockery::on(static fn (array $client): bool => $client['CLIENT_NICKNAME'] === $nickname && $client['CLIENT_CONNECTION_CLIENT_IP'] === $ip)
            );
            Redis::shouldReceive('expire')->once()->with('instance_1_client_'.$databaseId, 43200);
        }
        Redis::shouldNotReceive('del');
        Redis::shouldReceive('hmset')->once()->with(
            \Mockery::on(static fn (string $key): bool => str_starts_with($key, 'instance_1_client_ip_index:staging:')),
            [
                '192.168.2.17' => 42,
                '192.168.2.20' => 43,
                '192.168.2.34' => 44,
            ]
        );
        Redis::shouldReceive('expire')->once()->with(
            \Mockery::on(static fn (string $key): bool => str_starts_with($key, 'instance_1_client_ip_index:staging:')),
            43200
        );
        Redis::shouldReceive('rename')->once()->with(
            \Mockery::on(static fn (string $key): bool => str_starts_with($key, 'instance_1_client_ip_index:staging:')),
            'instance_1_client_ip_index'
        );
        Redis::shouldReceive('set')->once()->with('instance_1_client_default', 42, 'EX', 43200);

        $command->updateClientList();
    }

    public function test_keeps_existing_client_cache_when_publishing_a_refresh_fails(): void
    {
        $instance = new Instance();
        $instance->id = 1;
        $server = new class extends Server
        {
            public array $clients = [];

            public function __construct()
            {
            }

            public function clientListReset(): void
            {
            }

            public function clientList(array $filter = []): array
            {
                return $this->clients;
            }
        };
        $server->clients = [$this->client($server, 42, 'Max', '192.168.2.17')];

        $command = new class extends TeamspeakBot
        {
            public function setContextForTest(Instance $instance, Server $server): void
            {
                $this->instance = $instance;
                $this->virtualserver = $server;
            }

            public function __destruct()
            {
            }

            protected function message(string $log_level, string $message)
            {
            }
        };
        $command->setContextForTest($instance, $server);

        Redis::shouldReceive('hmset')->once()->with('instance_1_client_42', \Mockery::type('array'));
        Redis::shouldNotReceive('del');
        Redis::shouldReceive('expire')->once()->with('instance_1_client_42', 43200);
        Redis::shouldReceive('hmset')->once()->with(
            \Mockery::on(static fn (string $key): bool => str_starts_with($key, 'instance_1_client_ip_index:staging:')),
            ['192.168.2.17' => 42]
        )->andThrow(new \RuntimeException('Redis write failed'));
        Redis::shouldReceive('set')->once()->with('instance_1_client_default', 42, 'EX', 43200);

        $command->updateClientList();
    }

    private function client(Server $server, int $databaseId, string $nickname, string $ip): Client
    {
        return new Client($server, [
            'clid' => $databaseId - 30,
            'client_database_id' => $databaseId,
            'client_nickname' => new StringHelper($nickname),
            'client_servergroups' => '6',
            'client_version' => '3.6.2',
            'client_platform' => 'Linux',
            'client_country' => 'DE',
            'connection_client_ip' => $ip,
        ]);
    }
}
