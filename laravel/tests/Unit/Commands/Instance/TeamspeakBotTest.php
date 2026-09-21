<?php

namespace Tests\Unit\Commands\Instance;

use App\Console\Commands\Instance\TeamspeakBot;
use PHPUnit\Framework\TestCase;
use PlanetTeamSpeak\TeamSpeak3Framework\Helper\StringHelper;

class TeamspeakBotTest extends TestCase
{
    public function test_normalizes_framework_string_values_before_caching(): void
    {
        $command = new class extends TeamspeakBot
        {
            public function normalizeForTest(array $data): array
            {
                return $this->normalize_data_for_redis($data);
            }
        };

        $data = $command->normalizeForTest([
            'VIRTUALSERVER_NAME' => new StringHelper('Sample Server'),
            'VIRTUALSERVER_PLATFORM' => new StringHelper('Linux'),
            'VIRTUALSERVER_VERSION' => new StringHelper('3.13.7'),
            'VIRTUALSERVER_CLIENTSONLINE' => 4,
        ]);

        $this->assertSame([
            'VIRTUALSERVER_NAME' => 'Sample Server',
            'VIRTUALSERVER_PLATFORM' => 'Linux',
            'VIRTUALSERVER_VERSION' => '3.13.7',
            'VIRTUALSERVER_CLIENTSONLINE' => 4,
        ], $data);
    }
}
