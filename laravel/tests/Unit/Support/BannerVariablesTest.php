<?php

namespace Tests\Unit\Support;

use App\Support\BannerVariables;
use PHPUnit\Framework\TestCase;

class BannerVariablesTest extends TestCase
{
    public function test_it_keeps_named_scalar_banner_variables(): void
    {
        $variables = BannerVariables::sanitize([
            'virtualserver_name' => 'Example TeamSpeak',
            'virtualserver_clientsonline' => 12,
            'client_is_away' => false,
            'client_version' => new class implements \Stringable
            {
                public function __toString(): string
                {
                    return '3.13.7';
                }
            },
        ]);

        $this->assertSame([
            'VIRTUALSERVER_NAME' => 'Example TeamSpeak',
            'VIRTUALSERVER_CLIENTSONLINE' => 12,
            'CLIENT_IS_AWAY' => false,
            'CLIENT_VERSION' => '3.13.7',
        ], $variables);
    }

    public function test_it_discards_numeric_keys_and_non_scalar_values(): void
    {
        $variables = BannerVariables::sanitize([
            306 => ['connection_packets_sent_total' => 456],
            'connection_data' => ['unexpected' => 'array'],
            'connection_object' => new \stdClass,
            'virtualserver_name' => 'Example TeamSpeak',
        ]);

        $this->assertSame(['VIRTUALSERVER_NAME' => 'Example TeamSpeak'], $variables);
    }
}
