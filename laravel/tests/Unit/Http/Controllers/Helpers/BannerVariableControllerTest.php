<?php

namespace Tests\Unit\Http\Controllers\Helpers;

use App\Http\Controllers\Helpers\BannerVariableController;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use PlanetTeamSpeak\TeamSpeak3Framework\Helper\StringHelper;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Client;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;

class BannerVariableControllerTest extends TestCase
{
    public function test_time_variables_use_the_current_minute_without_ahead_of_time_offset(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 9, 23, 12, 34, 56, 'Europe/Berlin'));

        try {
            $variables = (new BannerVariableController(null))->get_current_time_data();
        } finally {
            Carbon::setTestNow();
        }

        $this->assertSame('12:34', $variables['CURRENT_TIME_EUROPE_BERLIN_HI']);
        $this->assertSame('10:34', $variables['CURRENT_TIME_UTC_HI']);
    }

    public function test_client_cache_data_uses_the_public_client_variable_names(): void
    {
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

        $nickname = new StringHelper('Ada Lovelace');
        $server->clients = [new Client($server, [
            'clid' => 7,
            'client_database_id' => 42,
            'client_nickname' => $nickname,
            'client_servergroups' => '6,7',
            'client_version' => '3.6.2',
            'client_platform' => 'Linux',
            'client_country' => 'DE',
            'connection_client_ip' => '203.0.113.42',
        ])];

        $clients = (new BannerVariableController($server))->get_current_client_list();

        $this->assertSame([
            'CLIENT_DATABASE_ID',
            'CLIENT_ID',
            'CLIENT_NICKNAME',
            'CLIENT_SERVERGROUPS',
            'CLIENT_VERSION',
            'CLIENT_PLATFORM',
            'CLIENT_COUNTRY',
            'CLIENT_CONNECTION_CLIENT_IP',
        ], array_keys($clients[42]));
        $this->assertSame($nickname, $clients[42]['CLIENT_NICKNAME']);
        $this->assertArrayNotHasKey('NICKNAME', $clients[42]);
    }

    public function test_virtualserver_variables_flatten_connection_information_rows(): void
    {
        $server = new class extends Server
        {
            public function __construct()
            {
            }

            public function getInfo(bool $extend = true, bool $convert = false): array
            {
                return ['virtualserver_name' => 'Example TeamSpeak'];
            }

            public function connectionInfo(): array
            {
                return [
                    ['connection_packets_sent_total' => 123],
                    [306 => ['connection_packets_received_total' => 456, 'connection_ping' => 42]],
                ];
            }
        };

        $variables = (new BannerVariableController($server))->get_current_virtualserver_info();

        $this->assertSame([
            'VIRTUALSERVER_NAME' => 'Example TeamSpeak',
            'CONNECTION_PACKETS_SENT_TOTAL' => 123,
            'CONNECTION_PACKETS_RECEIVED_TOTAL' => 456,
            'CONNECTION_PING' => 42,
        ], $variables);
        $this->assertArrayNotHasKey(0, $variables);
        $this->assertArrayNotHasKey(1, $variables);
        $this->assertArrayNotHasKey(306, $variables);
    }
}
