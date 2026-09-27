<?php

namespace App\Support\TeamSpeak;

use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;

/**
 * The only place for ServerQuery operations used by application features.
 *
 * Capabilities probe these methods, while the bot uses them to gather data.
 */
final class TeamSpeakQueryOperations
{
    public function readChannels(Server $server): array
    {
        return $server->channelList();
    }

    public function readClients(Server $server): array
    {
        $server->clientListReset();

        return $server->clientList(['client_type' => 0]);
    }

    public function readServerGroups(Server $server): array
    {
        $server->serverGroupListReset();

        return $server->serverGroupList(['type' => 1]);
    }

    public function readServerGroupMembers(Server $server, int $serverGroupId): array
    {
        return $server->serverGroupClientList($serverGroupId);
    }

    public function readServerInfo(Server $server): array
    {
        return $server->getInfo(true, true);
    }

    public function readConnectionInfo(Server $server): array
    {
        return $server->connectionInfo();
    }

    public function subscribeToServerEvents(Server $server): void
    {
        $server->notifyRegister('server');
    }

    public function moveQueryClientToChannel(Server $server, int $channelId): void
    {
        $server->clientMove($server->whoamiGet('client_id'), $channelId);
    }
}
