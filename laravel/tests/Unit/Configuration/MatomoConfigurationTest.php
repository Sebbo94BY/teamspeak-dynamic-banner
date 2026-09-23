<?php

namespace Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;

class MatomoConfigurationTest extends TestCase
{
    private const CONNECT_TIMEOUT_ENVIRONMENT_VARIABLE = 'MATOMO_CONNECT_TIMEOUT';

    private string|false $original_connect_timeout;

    protected function setUp(): void
    {
        parent::setUp();

        $this->original_connect_timeout = getenv(self::CONNECT_TIMEOUT_ENVIRONMENT_VARIABLE);
    }

    protected function tearDown(): void
    {
        $this->setConnectTimeoutEnvironmentVariable($this->original_connect_timeout);

        parent::tearDown();
    }

    public function test_connect_timeout_defaults_to_two_seconds(): void
    {
        $this->setConnectTimeoutEnvironmentVariable(false);

        $config = require __DIR__.'/../../../config/matomo.php';

        $this->assertSame(2, $config['connect_timeout']);
    }

    public function test_connect_timeout_can_be_configured_by_environment_variable(): void
    {
        $this->setConnectTimeoutEnvironmentVariable('7');

        $config = require __DIR__.'/../../../config/matomo.php';

        $this->assertSame(7, $config['connect_timeout']);
    }

    private function setConnectTimeoutEnvironmentVariable(string|false $value): void
    {
        if ($value === false) {
            putenv(self::CONNECT_TIMEOUT_ENVIRONMENT_VARIABLE);
            unset($_ENV[self::CONNECT_TIMEOUT_ENVIRONMENT_VARIABLE], $_SERVER[self::CONNECT_TIMEOUT_ENVIRONMENT_VARIABLE]);

            return;
        }

        putenv(self::CONNECT_TIMEOUT_ENVIRONMENT_VARIABLE.'='.$value);
        $_ENV[self::CONNECT_TIMEOUT_ENVIRONMENT_VARIABLE] = $value;
        $_SERVER[self::CONNECT_TIMEOUT_ENVIRONMENT_VARIABLE] = $value;
    }
}
