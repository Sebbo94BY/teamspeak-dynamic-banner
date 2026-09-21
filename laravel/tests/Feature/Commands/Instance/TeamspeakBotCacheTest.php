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

        Redis::shouldReceive('del')->once()->with('instance_1_client_ip_index');
        foreach ([
            42 => ['Max', '192.168.2.17'],
            43 => ['Peter', '192.168.2.20'],
            44 => ['Ulrike', '192.168.2.34'],
        ] as $databaseId => [$nickname, $ip]) {
            Redis::shouldReceive('del')->once()->with('instance_1_client_'.$databaseId);
            Redis::shouldReceive('hmset')->once()->with(
                'instance_1_client_'.$databaseId,
                \Mockery::on(static fn (array $client): bool => $client['CLIENT_NICKNAME'] === $nickname && $client['CLIENT_CONNECTION_CLIENT_IP'] === $ip)
            );
            Redis::shouldReceive('expire')->once()->with('instance_1_client_'.$databaseId, 43200);
            Redis::shouldReceive('hset')->once()->with('instance_1_client_ip_index', $ip, $databaseId);
        }
        Redis::shouldReceive('set')->once()->with('instance_1_client_default', 42, 'EX', 43200);
        Redis::shouldReceive('expire')->once()->with('instance_1_client_ip_index', 43200);

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
