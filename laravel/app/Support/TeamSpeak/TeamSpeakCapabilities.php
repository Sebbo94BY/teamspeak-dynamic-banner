<?php

namespace App\Support\TeamSpeak;

use App\Models\Instance;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;

/**
 * The authoritative contract of all ServerQuery operations used by the app.
 *
 * Keep a new TeamSpeak feature out of the bot until its capability is added
 * here. The health check and runtime error translation derive from this list.
 */
final class TeamSpeakCapabilities
{
    public function __construct(private readonly TeamSpeakQueryOperations $operations)
    {
    }

    /** @return array<int, TeamSpeakCapability> */
    public function all(): array
    {
        return [
            new TeamSpeakCapability(
                key: 'channels',
                label: 'health_check_channels',
                permissions: ['b_virtualserver_channel_list'],
                queryCommands: ['channellist'],
                probe: function (Server $server, array &$context): void {
                    $context['channel_list'] = $this->operations->readChannels($server);
                },
            ),
            new TeamSpeakCapability(
                key: 'clients',
                label: 'health_check_clients',
                permissions: ['b_virtualserver_client_list'],
                queryCommands: ['clientlist'],
                probe: fn (Server $server, array &$context) => $this->operations->readClients($server),
            ),
            new TeamSpeakCapability(
                key: 'servergroups',
                label: 'health_check_servergroups',
                permissions: ['b_virtualserver_servergroup_list'],
                queryCommands: ['servergrouplist'],
                probe: function (Server $server, array &$context): void {
                    $context['server_groups'] = $this->operations->readServerGroups($server);
                },
            ),
            new TeamSpeakCapability(
                key: 'servergroup_members',
                label: 'health_check_servergroup_members',
                permissions: ['b_virtualserver_servergroup_client_list'],
                queryCommands: ['servergroupclientlist'],
                probe: function (Server $server, array &$context): void {
                    $groups = $context['server_groups'] ?? $this->operations->readServerGroups($server);

                    foreach ($groups as $group) {
                        $this->operations->readServerGroupMembers($server, $group->sgid);
                    }
                },
            ),
            new TeamSpeakCapability(
                key: 'serverinfo',
                label: 'health_check_serverinfo',
                permissions: ['b_virtualserver_info_view'],
                queryCommands: ['serverinfo'],
                probe: fn (Server $server, array &$context) => $this->operations->readServerInfo($server),
            ),
            new TeamSpeakCapability(
                key: 'connectioninfo',
                label: 'health_check_connectioninfo',
                permissions: ['b_virtualserver_connectioninfo_view'],
                queryCommands: ['serverrequestconnectioninfo'],
                probe: fn (Server $server, array &$context) => $this->operations->readConnectionInfo($server),
            ),
            new TeamSpeakCapability(
                key: 'events',
                label: 'health_check_events',
                permissions: ['b_virtualserver_notify_register'],
                queryCommands: ['servernotifyregister'],
                probe: fn (Server $server, array &$context) => $this->operations->subscribeToServerEvents($server),
            ),
        ];
    }

    public function permissionForQueryCommand(string $message): ?string
    {
        $message = strtolower($message);

        foreach ($this->all() as $capability) {
            foreach ($capability->queryCommands as $command) {
                if (str_contains($message, $command) && count($capability->permissions) === 1) {
                    return $capability->permissions[0];
                }
            }
        }

        if (str_contains($message, 'clientmove')) {
            return 'b_virtualserver_client_move';
        }

        return null;
    }

    /** @return array<int, string> */
    public function connectionPermissions(Instance $instance): array
    {
        return is_null($instance->default_channel_id) ? [] : ['b_virtualserver_client_move'];
    }

    /** @return array<int, string> */
    public function connectionQueryCommands(): array
    {
        return ['clientmove'];
    }
}
