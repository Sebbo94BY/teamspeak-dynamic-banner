<?php

namespace App\Support\TeamSpeak;

use PlanetTeamSpeak\TeamSpeak3Framework\Exception\TransportException;
use Throwable;

/**
 * Identifies errors after which a ServerQuery session must not be reused.
 */
final class TeamSpeakConnectionFailure
{
    public static function makesSessionUnusable(Throwable $exception): bool
    {
        return $exception instanceof TransportException
            || str_contains(strtolower($exception->getMessage()), 'invalid serverid');
    }
}
