<?php

namespace Tests\Unit\Http\Controllers\Helpers;

use App\Http\Controllers\Helpers\BannerVariableController;
use PHPUnit\Framework\TestCase;
use PlanetTeamSpeak\TeamSpeak3Framework\Helper\StringHelper;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Client;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;

class BannerVariableControllerTest extends TestCase
{
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
}
