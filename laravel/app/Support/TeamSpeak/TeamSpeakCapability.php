<?php

namespace App\Support\TeamSpeak;

use Closure;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;

/**
 * A single ServerQuery capability required by the application.
 *
 * Adding a TeamSpeak feature means adding its real probe and permissions here.
 */
final readonly class TeamSpeakCapability
{
    /**
     * @param array<int, string> $permissions
     * @param array<int, string> $queryCommands
     * @param Closure(Server, array<string, mixed>&): void $probe
     */
    public function __construct(
        public string $key,
        public string $label,
        public array $permissions,
        public array $queryCommands,
        private Closure $probe,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function probe(Server $server, array &$context): void
    {
        ($this->probe)($server, $context);
    }
}
