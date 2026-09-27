<?php

namespace App\Support;

final class TeamSpeakBotRecovery
{
    /** @var array<int, int> */
    private const BACKOFF_SECONDS = [30, 60, 300];

    public function delayForAttempt(int $attempt): int
    {
        return self::BACKOFF_SECONDS[min(max($attempt, 1), count(self::BACKOFF_SECONDS)) - 1];
    }
}
