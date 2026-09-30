<?php

namespace Tests\Unit\Support;

use App\Support\TeamSpeakBotRecovery;
use PHPUnit\Framework\TestCase;

class TeamSpeakBotRecoveryTest extends TestCase
{
    public function test_it_uses_bounded_exponential_recovery_delays(): void
    {
        $recovery = new TeamSpeakBotRecovery;

        $this->assertSame(30, $recovery->delayForAttempt(1));
        $this->assertSame(60, $recovery->delayForAttempt(2));
        $this->assertSame(300, $recovery->delayForAttempt(3));
        $this->assertSame(300, $recovery->delayForAttempt(99));
    }
}
