<?php

namespace Tests\Unit\Http\Controllers\Helpers;

use App\Http\Controllers\Helpers\TeamSpeakVirtualserver;
use App\Models\Instance;
use PHPUnit\Framework\TestCase;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\ServerQueryException;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;
use PlanetTeamSpeak\TeamSpeak3Framework\TeamSpeak3;

class TeamSpeakVirtualserverTest extends TestCase
{
    public function test_reconnect_after_a_nickname_conflict_never_returns_the_server_from_the_previous_session(): void
    {
        $previousServer = new class extends Server
        {
            public function __construct()
            {
            }
        };

        $freshServer = new class extends Server
        {
            public function __construct()
            {
            }
        };

        $helper = new class($this->instance(), $previousServer, $freshServer) extends TeamSpeakVirtualserver
        {
            public int $connectionAttempts = 0;

            public function __construct(Instance $instance, Server $previousServer, private Server $freshServer)
            {
                parent::__construct($instance);
                $this->virtualserver = $previousServer;
            }

            protected function create_virtualserver_connection(TeamSpeak3 $framework, string $connection_uri): Server
            {
                $this->connectionAttempts++;

                if ($this->connectionAttempts === 1) {
                    throw new ServerQueryException('nickname is already in use', 513);
                }

                return $this->freshServer;
            }
        };

        $this->assertSame($freshServer, $helper->get_virtualserver_connection());
        $this->assertSame($freshServer, $helper->virtualserver);
        $this->assertSame(2, $helper->connectionAttempts);
    }

    public function test_successful_reconnect_replaces_the_server_from_the_previous_session(): void
    {
        $previousServer = new class extends Server
        {
            public function __construct()
            {
            }
        };
        $freshServer = new class extends Server
        {
            public function __construct()
            {
            }
        };

        $helper = new class($this->instance(), $previousServer, $freshServer) extends TeamSpeakVirtualserver
        {
            public function __construct(Instance $instance, Server $previousServer, private Server $freshServer)
            {
                parent::__construct($instance);
                $this->virtualserver = $previousServer;
            }

            protected function create_virtualserver_connection(TeamSpeak3 $framework, string $connection_uri): Server
            {
                return $this->freshServer;
            }
        };

        $this->assertSame($freshServer, $helper->get_virtualserver_connection());
        $this->assertSame($freshServer, $helper->virtualserver);
    }

    public function test_temporary_nickname_is_used_before_connecting_for_health_checks(): void
    {
        $server = new class extends Server
        {
            public function __construct()
            {
            }
        };

        $helper = new class($this->instance(), $server) extends TeamSpeakVirtualserver
        {
            public ?string $connectionUri = null;

            public function __construct(Instance $instance, private Server $server)
            {
                parent::__construct($instance);
                $this->with_temporary_nickname();
            }

            protected function create_virtualserver_connection(TeamSpeak3 $framework, string $connection_uri): Server
            {
                $this->connectionUri = $connection_uri;

                return $this->server;
            }
        };

        $helper->get_virtualserver_connection();
        parse_str((string) parse_url((string) $helper->connectionUri, PHP_URL_QUERY), $query);

        $this->assertIsString($query['nickname'] ?? null);
        $this->assertNotSame('banner-bot', $query['nickname']);
        $this->assertStringStartsWith('banner-bot-HC-', $query['nickname']);
        $this->assertLessThanOrEqual(30, strlen($query['nickname']));
    }

    public function test_temporary_nickname_respects_the_team_speak_length_limit(): void
    {
        $instance = $this->instance();
        $instance->client_nickname = str_repeat('x', 30);
        $server = new class extends Server
        {
            public function __construct()
            {
            }
        };

        $helper = new class($instance, $server) extends TeamSpeakVirtualserver
        {
            public ?string $connectionUri = null;

            public function __construct(Instance $instance, private Server $server)
            {
                parent::__construct($instance);
                $this->with_temporary_nickname();
            }

            protected function create_virtualserver_connection(TeamSpeak3 $framework, string $connection_uri): Server
            {
                $this->connectionUri = $connection_uri;

                return $this->server;
            }
        };

        $helper->get_virtualserver_connection();
        parse_str((string) parse_url((string) $helper->connectionUri, PHP_URL_QUERY), $query);

        $this->assertSame(30, strlen($query['nickname']));
    }

    public function test_nickname_conflict_is_detected_from_the_error_message(): void
    {
        $server = new class extends Server
        {
            public function __construct()
            {
            }
        };

        $helper = new class($this->instance(), $server) extends TeamSpeakVirtualserver
        {
            public int $connectionAttempts = 0;

            public array $connectionUris = [];

            public function __construct(Instance $instance, private Server $server)
            {
                parent::__construct($instance);
            }

            protected function create_virtualserver_connection(TeamSpeak3 $framework, string $connection_uri): Server
            {
                $this->connectionAttempts++;
                $this->connectionUris[] = $connection_uri;

                if ($this->connectionAttempts === 1) {
                    throw new ServerQueryException('nickname is already in use', 0);
                }

                return $this->server;
            }
        };

        $this->assertSame($server, $helper->get_virtualserver_connection());
        $this->assertSame(2, $helper->connectionAttempts);
        $this->assertStringContainsString('nickname=banner-bot-RC-', $helper->connectionUris[1]);
    }

    private function instance(): Instance
    {
        $instance = new class extends Instance
        {
            protected function casts(): array
            {
                return [];
            }
        };
        $instance->forceFill([
            'serverquery_username' => 'query-user',
            'serverquery_password' => 'query-password',
            'host' => 'ts.example.test',
            'serverquery_port' => '10011',
            'is_ssh' => false,
            'voice_port' => '9987',
            'client_nickname' => 'banner-bot',
        ]);

        return $instance;
    }
}
