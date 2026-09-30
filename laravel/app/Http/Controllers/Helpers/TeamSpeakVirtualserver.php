<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use App\Support\TeamSpeak\TeamSpeakQueryOperations;
use Exception;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\ServerQueryException;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\TransportException;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;
use PlanetTeamSpeak\TeamSpeak3Framework\TeamSpeak3;

class TeamSpeakVirtualserver extends Controller
{
    private const MAX_CLIENT_NICKNAME_LENGTH = 30;

    private const NICKNAME_ALREADY_IN_USE_ERROR_CODE = 513;

    private const HEALTHCHECK_NICKNAME_MARKER = '-HC-';

    private const RECONNECT_NICKNAME_MARKER = '-RC-';

    /**
     * Class properties
     */
    private string $serverquery_username;

    private string $serverquery_password;

    private string $host;

    private string $serverquery_port;

    private bool $is_ssh;

    private string $voice_port;

    private string $client_nickname;

    private ?int $default_channel_id = null;

    /**
     * Use a unique nickname for short-lived connections such as health checks.
     * The running bot keeps the configured nickname whenever it is available.
     */
    private bool $use_temporary_nickname = false;

    private string $temporary_nickname_marker = self::RECONNECT_NICKNAME_MARKER;

    private TeamSpeakQueryOperations $operations;

    public Server $virtualserver;

    /**
     * The class constructor
     */
    public function __construct(Instance $instance)
    {
        $this->operations = new TeamSpeakQueryOperations;
        $this->serverquery_username = $instance->serverquery_username;
        $this->serverquery_password = $instance->serverquery_password;
        $this->host = $instance->host;
        $this->serverquery_port = $instance->serverquery_port;
        $this->is_ssh = $instance->is_ssh;
        $this->voice_port = $instance->voice_port;
        $this->client_nickname = $instance->client_nickname;
        $this->default_channel_id = $instance->default_channel_id;
    }

    /**
     * Makes this short-lived connection use a unique nickname immediately.
     */
    public function with_temporary_nickname(): self
    {
        $this->use_temporary_nickname = true;
        $this->temporary_nickname_marker = self::HEALTHCHECK_NICKNAME_MARKER;

        return $this;
    }

    /**
     * The class destructor
     */
    public function __destruct()
    {
        if (isset($this->virtualserver) and is_resource($this->virtualserver)) {
            $this->virtualserver->disconnect();
        }
    }

    /**
     * Creates a new virtual-server connection for one connection attempt.
     *
     * Kept separate from the retry logic so an attempt cannot accidentally
     * reuse the virtual-server object of an earlier, disconnected session.
     */
    protected function create_virtualserver_connection(TeamSpeak3 $framework, string $connection_uri): Server
    {
        return $framework->factory($connection_uri);
    }

    /**
     * Returns the connection URI for the TS3PHPFramework
     */
    protected function get_connection_uri(bool $blocking = true, bool $append_random_number_to_nickname = false): string
    {
        $connection_uri = 'serverquery://'.rawurlencode($this->serverquery_username).':'.rawurlencode($this->serverquery_password).'@';

        if (filter_var($this->host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // IPv6 addresses must be enclosed in brackets.
            $connection_uri = $connection_uri.'['.$this->host.']';
        } else {
            // IPv4 address or domain
            $connection_uri = $connection_uri.$this->host;
        }

        $connection_uri = $connection_uri.":$this->serverquery_port/?server_port=$this->voice_port";

        $use_temporary_nickname = $this->use_temporary_nickname || $append_random_number_to_nickname;
        $nickname = $use_temporary_nickname
            ? $this->temporary_nickname()
            : $this->client_nickname;
        $connection_uri .= '&nickname='.rawurlencode($nickname);

        if ($this->is_ssh) {
            $connection_uri = $connection_uri.'&ssh=1';
        }

        if (! $blocking) {
            $connection_uri = $connection_uri.'&blocking=0';
        }

        $connection_uri = $connection_uri.'#no_query_clients';

        return $connection_uri;
    }

    /**
     * Creates a unique nickname that stays within TeamSpeak's 30-character limit.
     */
    protected function temporary_nickname(): string
    {
        $suffix = $this->temporary_nickname_marker.strtoupper(bin2hex(random_bytes(3)));
        $base_length = self::MAX_CLIENT_NICKNAME_LENGTH - strlen($suffix);
        $base = function_exists('mb_substr')
            ? mb_substr($this->client_nickname, 0, $base_length, 'UTF-8')
            : substr($this->client_nickname, 0, $base_length);

        return $base.$suffix;
    }

    protected function is_nickname_already_in_use(ServerQueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return (int) $exception->getCode() === self::NICKNAME_ALREADY_IN_USE_ERROR_CODE
            || (str_contains($message, 'nickname') && str_contains($message, 'already in use'));
    }

    /**
     * Connects to an instance and returns the virtualserver object.
     */
    public function get_virtualserver_connection(bool $blocking = true): Server
    {
        $TS3PHPFramework = new TeamSpeak3();

        // Do not let a failed reconnect fall back to a Server instance from a
        // previous query session. That stale object may later fail with
        // "invalid serverID" although the configured voice port is correct.
        unset($this->virtualserver);

        $connection_attempt = 1;
        $maximum_connection_attempts = 3;
        $serverquery_exception_nickname_already_in_use = $this->use_temporary_nickname;
        $last_serverquery_exception = null;
        while ($connection_attempt <= $maximum_connection_attempts) {
            try {
                $virtualserver = $this->create_virtualserver_connection(
                    $TS3PHPFramework,
                    $this->get_connection_uri($blocking, $serverquery_exception_nickname_already_in_use)
                );
                $this->virtualserver = $virtualserver;
            } catch (TransportException $transport_exception) {
                throw new TransportException($transport_exception->getMessage(), $transport_exception->getCode());
            } catch (ServerQueryException $serverquery_exception) {
                if ($this->is_nickname_already_in_use($serverquery_exception)) {
                    // Error: nickname is already in use
                    $serverquery_exception_nickname_already_in_use = true;
                    $last_serverquery_exception = $serverquery_exception;
                } else {
                    throw new ServerQueryException($serverquery_exception->getMessage(), $serverquery_exception->getCode());
                }
            } finally {
                $connection_attempt++;
            }

            // leave the loop once we have a connection
            if (isset($virtualserver)) {
                break;
            }
        }

        if (! isset($this->virtualserver)) {
            if (! is_null($last_serverquery_exception)) {
                throw new ServerQueryException($last_serverquery_exception->getMessage(), $last_serverquery_exception->getCode());
            }

            throw new Exception('Failed to establish a connection to the TeamSpeak host.');
        }

        if (! is_null($this->default_channel_id)) {
            try {
                $this->operations->moveQueryClientToChannel($this->virtualserver, $this->default_channel_id);
            } catch (ServerQueryException $serverquery_exception) {
                throw new ServerQueryException($serverquery_exception->getMessage(), $serverquery_exception->getCode());
            }
        }

        return $this->virtualserver;
    }
}
