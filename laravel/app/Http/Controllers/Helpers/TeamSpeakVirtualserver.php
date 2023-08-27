<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use Exception;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\ServerQueryException;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\TransportException;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;
use PlanetTeamSpeak\TeamSpeak3Framework\TeamSpeak3;

class TeamSpeakVirtualserver extends Controller
{
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

    public Server $virtualserver;

    /**
     * The class constructor
     */
    public function __construct(Instance $instance)
    {
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
     * The class destructor
     */
    public function __destruct()
    {
        if (isset($this->virtualserver) and is_resource($this->virtualserver)) {
            $this->virtualserver->disconnect();
        }
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

        $connection_uri = ($append_random_number_to_nickname) ? $connection_uri.'&nickname='.rawurlencode($this->client_nickname.rand(0, 9)) : $connection_uri.'&nickname='.rawurlencode($this->client_nickname);

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
     * Connects to an instance and returns the virtualserver object.
     */
    public function get_virtualserver_connection(bool $blocking = true): Server
    {
        $TS3PHPFramework = new TeamSpeak3();

        $connection_attempt = 1;
        $maximum_connection_attempts = 3;
        $serverquery_exception_nickname_already_in_use = false;
        while ($connection_attempt < $maximum_connection_attempts) {
            try {
                $this->virtualserver = $TS3PHPFramework->factory($this->get_connection_uri($blocking, ($serverquery_exception_nickname_already_in_use) ? true : false));
            } catch (TransportException $transport_exception) {
                throw new TransportException($transport_exception->getMessage(), $transport_exception->getCode());
            } catch (ServerQueryException $serverquery_exception) {
                if ($serverquery_exception->getCode() == 513) {
                    // Error: nickname is already in use
                    $serverquery_exception_nickname_already_in_use = true;
                } else {
                    throw new ServerQueryException($serverquery_exception->getMessage(), $serverquery_exception->getCode());
                }
            } finally {
                $connection_attempt++;
            }

            // leave the loop once we have a connection
            if (isset($this->virtualserver)) {
                break;
            }
        }

        if (! isset($this->virtualserver)) {
            throw new Exception('Failed to establish a connection to the TeamSpeak host.');
        }

        if (! is_null($this->default_channel_id)) {
            try {
                $this->virtualserver->clientMove($this->virtualserver->whoamiGet('client_id'), $this->default_channel_id);
            } catch (ServerQueryException $serverquery_exception) {
                throw new ServerQueryException($serverquery_exception->getMessage(), $serverquery_exception->getCode());
            }
        }

        return $this->virtualserver;
    }
}
