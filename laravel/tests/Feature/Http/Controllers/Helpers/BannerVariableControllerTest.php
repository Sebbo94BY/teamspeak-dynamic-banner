<?php

namespace Tests\Feature\Http\Controllers\Helpers;

use App\Http\Controllers\Helpers\BannerVariableController;
use App\Models\Instance;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class BannerVariableControllerTest extends TestCase
{
    public function test_client_variables_are_selected_by_request_ip(): void
    {
        $instance = new Instance();
        $instance->id = 1;

        $clientIdsByIp = [
            '192.168.2.17' => '42',
            '192.168.2.20' => '43',
            '192.168.2.34' => '44',
        ];
        $clients = [
            '42' => ['CLIENT_NICKNAME' => 'Max', 'CLIENT_CONNECTION_CLIENT_IP' => '192.168.2.17'],
            '43' => ['CLIENT_NICKNAME' => 'Peter', 'CLIENT_CONNECTION_CLIENT_IP' => '192.168.2.20'],
            '44' => ['CLIENT_NICKNAME' => 'Ulrike', 'CLIENT_CONNECTION_CLIENT_IP' => '192.168.2.34'],
        ];

        Redis::shouldReceive('hget')
            ->times(3)
            ->with('instance_1_client_ip_index', \Mockery::type('string'))
            ->andReturnUsing(static fn (string $key, string $ip): ?string => $clientIdsByIp[$ip] ?? null);
        Redis::shouldReceive('hgetall')
            ->times(3)
            ->andReturnUsing(static function (string $key) use ($clients): array {
                preg_match('/_client_(\d+)$/', $key, $matches);

                return $clients[$matches[1]];
            });

        $controller = new BannerVariableController(null);

        foreach (['192.168.2.17' => 'Max', '192.168.2.20' => 'Peter', '192.168.2.34' => 'Ulrike'] as $ip => $nickname) {
            $this->assertSame($nickname, $controller->get_client_specific_info_from_cache($instance, $ip)['CLIENT_NICKNAME']);
        }
    }
}
