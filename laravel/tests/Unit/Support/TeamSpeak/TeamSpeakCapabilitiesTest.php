<?php

namespace Tests\Unit\Support\TeamSpeak;

use App\Models\Instance;
use App\Support\InstanceHealthCheck;
use App\Support\TeamSpeak\TeamSpeakCapabilities;
use App\Support\TeamSpeak\TeamSpeakQueryOperations;
use PHPUnit\Framework\TestCase;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\ServerQueryException;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\TransportException;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;

class TeamSpeakCapabilitiesTest extends TestCase
{
    public function test_every_registered_capability_has_a_unique_key_probe_permission_and_query_command(): void
    {
        $capabilities = (new TeamSpeakCapabilities(new TeamSpeakQueryOperations))->all();

        $this->assertSame(
            ['channels', 'clients', 'servergroups', 'servergroup_members', 'serverinfo', 'connectioninfo', 'events'],
            array_map(fn ($capability) => $capability->key, $capabilities),
        );

        foreach ($capabilities as $capability) {
            $this->assertNotSame('', $capability->label);
            $this->assertNotEmpty($capability->permissions);
            $this->assertNotEmpty($capability->queryCommands);
        }
    }

    public function test_every_query_command_used_by_the_operations_layer_is_registered(): void
    {
        $capabilities = (new TeamSpeakCapabilities(new TeamSpeakQueryOperations))->all();
        $commands = [
            ...array_merge(...array_map(fn ($capability) => $capability->queryCommands, $capabilities)),
            ...(new TeamSpeakCapabilities(new TeamSpeakQueryOperations))->connectionQueryCommands(),
        ];

        $this->assertSame([
            'channellist',
            'clientlist',
            'servergrouplist',
            'servergroupclientlist',
            'serverinfo',
            'serverrequestconnectioninfo',
            'servernotifyregister',
            'clientmove',
        ], $commands);
    }

    public function test_default_channel_connection_requirement_is_centralized(): void
    {
        $capabilities = new TeamSpeakCapabilities(new TeamSpeakQueryOperations);
        $instance = new Instance(['default_channel_id' => 42]);

        $this->assertSame(['b_virtualserver_client_move'], $capabilities->connectionPermissions($instance));
    }

    public function test_raw_serverquery_operations_are_confined_to_the_operations_layer(): void
    {
        $appPath = dirname(__DIR__, 4).'/app';
        $operationsPath = realpath($appPath.'/Support/TeamSpeak/TeamSpeakQueryOperations.php');
        $violations = [];

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($appPath)) as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php' || $file->getRealPath() === $operationsPath) {
                continue;
            }

            if (preg_match('/->(channelList|clientList|serverGroupList|serverGroupClientList|getInfo|connectionInfo|notifyRegister|clientMove)\s*\(/', file_get_contents($file->getRealPath()))) {
                $violations[] = $file->getRealPath();
            }
        }

        $this->assertSame([], $violations, 'ServerQuery operations must be added to TeamSpeakQueryOperations and TeamSpeakCapabilities.');
    }

    public function test_a_lost_connection_stops_remaining_capability_checks(): void
    {
        $server = new class extends Server
        {
            public function __construct()
            {
            }

            public function channelList(array $filter = []): array
            {
                throw new TransportException('connection to server lost');
            }
        };
        $health = new InstanceHealthCheck(new TeamSpeakCapabilities(new TeamSpeakQueryOperations));

        $result = $health->checkConnected($server);

        $this->assertTrue($result['connection_unusable']);
        $this->assertFalse($result['healthy']);
        $this->assertCount(1, $result['checks']);
        $this->assertSame('health_check_channels', $result['checks'][0]['label']);
    }

    public function test_an_invalid_server_id_stops_remaining_capability_checks(): void
    {
        $server = new class extends Server
        {
            public function __construct()
            {
            }

            public function channelList(array $filter = []): array
            {
                throw new ServerQueryException('invalid serverID');
            }
        };
        $health = new InstanceHealthCheck(new TeamSpeakCapabilities(new TeamSpeakQueryOperations));

        $result = $health->checkConnected($server);

        $this->assertTrue($result['connection_unusable']);
        $this->assertFalse($result['healthy']);
        $this->assertCount(1, $result['checks']);
        $this->assertSame('health_check_channels', $result['checks'][0]['label']);
    }
}
