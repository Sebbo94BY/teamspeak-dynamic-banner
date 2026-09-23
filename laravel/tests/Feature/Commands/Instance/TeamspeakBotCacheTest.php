<?php

namespace Tests\Feature\Commands\Instance;

use App\Console\Commands\Instance\TeamspeakBot;
use App\Http\Controllers\Helpers\TeamSpeakVirtualserver;
use App\Models\Instance;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use PlanetTeamSpeak\TeamSpeak3Framework\Helper\StringHelper;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Client;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;
use Tests\TestCase;

class TeamspeakBotCacheTest extends TestCase
{
    public function test_polled_cache_ttls_allow_multiple_failed_refreshes(): void
    {
        $constants = [];
        foreach ((new \ReflectionClass(TeamspeakBot::class))->getReflectionConstants() as $constant) {
            $constants[$constant->getName()] = $constant->getValue();
        }

        $this->assertGreaterThanOrEqual(
            $constants['DATETIME_REFRESH_INTERVAL_SECONDS'] * 4,
            $constants['DATETIME_CACHE_TTL_SECONDS']
        );
        $this->assertGreaterThanOrEqual(
            $constants['SERVERGROUP_REFRESH_INTERVAL_SECONDS'] * 3,
            $constants['SERVERGROUP_CACHE_TTL_SECONDS']
        );
        $this->assertGreaterThanOrEqual(
            $constants['VIRTUALSERVER_REFRESH_INTERVAL_SECONDS'] * 4,
            $constants['VIRTUALSERVER_CACHE_TTL_SECONDS']
        );
        $this->assertGreaterThanOrEqual(60 * 60 * 12, $constants['CLIENT_CACHE_TTL_SECONDS']);
    }

    public function test_full_cache_refresh_loads_every_banner_data_source(): void
    {
        $command = new class extends TeamspeakBot
        {
            public array $refreshedSources = [];

            public function refreshForTest(): void
            {
                $this->refresh_cached_data();
            }

            public function updateDatetime()
            {
                $this->refreshedSources[] = 'datetime';
            }

            public function updateClientList()
            {
                $this->refreshedSources[] = 'clients';
            }

            public function updateServergroupList()
            {
                $this->refreshedSources[] = 'servergroups';
            }

            public function updateVirtualserverInfo()
            {
                $this->refreshedSources[] = 'virtualserver';
            }

            public function __destruct()
            {
            }
        };

        $command->refreshForTest();

        $this->assertSame(['datetime', 'clients', 'servergroups', 'virtualserver'], $command->refreshedSources);
    }

    public function test_startup_retries_the_complete_cache_refresh(): void
    {
        $command = new class extends TeamspeakBot
        {
            public int $cacheRefreshes = 0;

            public int $retryWaits = 0;

            public function retryCacheForTest(): void
            {
                $this->retry_cache_refresh_during_startup();
            }

            public function refreshCacheForTest(): void
            {
                $this->refresh_cached_data();
            }

            protected function refresh_cached_data()
            {
                $this->cacheRefreshes++;
                $this->cache_refresh_status = [
                    'datetime' => true,
                    'clients' => $this->cacheRefreshes === 2,
                    'servergroups' => $this->cacheRefreshes === 2,
                    'virtualserver' => $this->cacheRefreshes === 2,
                ];

                return ! in_array(false, $this->cache_refresh_status, true);
            }

            protected function wait_before_startup_refresh_retry(): void
            {
                $this->retryWaits++;
            }

            protected function message(string $log_level, string $message)
            {
            }

            public function __destruct()
            {
            }
        };

        // The initial complete refresh is performed before this retry loop.
        $command->refreshCacheForTest();
        $command->retryCacheForTest();

        $this->assertSame(2, $command->cacheRefreshes);
        $this->assertSame(1, $command->retryWaits);
    }

    public function test_startup_reconnects_before_retrying_a_lost_teamspeak_connection(): void
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

                return $this->server;
            }
        };
        $command = new class extends TeamspeakBot
        {
            public int $cacheRefreshes = 0;

            public function retryCacheForTest(TeamSpeakVirtualserver $helper): void
            {
                $this->cache_refresh_status = ['clients' => false];
                $this->teamspeak_connection_lost = true;
                $this->retry_cache_refresh_during_startup($helper);
            }

            protected function refresh_cached_data()
            {
                $this->cacheRefreshes++;
                $this->cache_refresh_status = ['datetime' => true, 'clients' => true, 'servergroups' => true, 'virtualserver' => true];

                return true;
            }

            protected function wait_before_startup_refresh_retry(): void
            {
            }

            protected function message(string $log_level, string $message)
            {
            }

            public function __destruct()
            {
            }
        };

        $command->retryCacheForTest($helper);

        $this->assertSame(1, $helper->connections);
        $this->assertSame(1, $command->cacheRefreshes);
    }

    public function test_startup_stops_retrying_after_the_configured_attempts(): void
    {
        $command = new class extends TeamspeakBot
        {
            public int $cacheRefreshes = 0;

            public int $retryWaits = 0;

            public array $messages = [];

            public function startRetryForTest(): void
            {
                $this->cache_refresh_status = ['clients' => false];
                $this->retry_cache_refresh_during_startup();
            }

            protected function refresh_cached_data()
            {
                $this->cacheRefreshes++;
                $this->cache_refresh_status = ['datetime' => false, 'clients' => false, 'servergroups' => false, 'virtualserver' => false];

                return false;
            }

            protected function wait_before_startup_refresh_retry(): void
            {
                $this->retryWaits++;
            }

            protected function message(string $log_level, string $message)
            {
                $this->messages[] = $log_level.': '.$message;
            }

            public function __destruct()
            {
            }
        };

        $command->startRetryForTest();

        $this->assertSame(2, $command->cacheRefreshes);
        $this->assertSame(2, $command->retryWaits);
        $this->assertSame([
            'WARNING: Retrying the complete TeamSpeak cache refresh (attempt 2/3).',
            'WARNING: Retrying the complete TeamSpeak cache refresh (attempt 3/3).',
        ], $command->messages);
    }

    public function test_regular_cache_refresh_failures_are_throttled_in_the_log(): void
    {
        Cache::store('file')->clear();
        Log::spy();
        $instance = new Instance();
        $instance->id = 123;
        $command = new class extends TeamspeakBot
        {
            public function logFailureForTest(Instance $instance): void
            {
                $this->instance = $instance;
                $this->log_cache_refresh_failure('client');
                $this->log_cache_refresh_failure('client');
            }

            public function __destruct()
            {
            }
        };

        $command->logFailureForTest($instance);

        Log::shouldHaveReceived('error')->once()->with('TeamSpeak client cache refresh failed for instance 123');
    }

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
