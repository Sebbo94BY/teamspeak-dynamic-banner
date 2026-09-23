<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use App\Models\TwitchApi;
use App\Models\TwitchStreamer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\ServerQueryException;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;
use Predis\PredisException;
use RedisException;

class BannerVariableController extends Controller
{
    /**
     * Class properties
     */
    private ?Server $virtualserver = null;

    /**
     * The class constructor
     */
    public function __construct(?Server $virtualserver)
    {
        $this->virtualserver = $virtualserver;
    }

    /**
     * The class destructor
     */
    public function __destruct()
    {
        unset($this->virtualserver);
    }

    /**
     * Returns various datetime formatted values.
     */
    public function get_current_time_data(): array
    {
        $current_datetime = Carbon::now()->addMinute();

        $datetimes = [
            'current_time_utc_hi' => $current_datetime->setTimezone('UTC')->format('H:i'),
            'current_time_europe_berlin_hi' => $current_datetime->setTimezone('Europe/Berlin')->format('H:i'),
            'current_date_utc_ymd' => $current_datetime->setTimezone('UTC')->format('Y-m-d'),
            'current_date_europe_berlin_dmy' => $current_datetime->setTimezone('Europe/Berlin')->format('d.m.Y'),
            'current_datetime_utc_ymd_hi' => $current_datetime->setTimezone('UTC')->format('Y-m-d H:i'),
            'current_datetime_europe_berlin_dmy_hi' => $current_datetime->setTimezone('Europe/Berlin')->format('d.m.Y H:i'),
        ];

        return array_change_key_case($datetimes, CASE_UPPER);
    }

    /**
     * Returns a client list with various information.
     */
    public function get_current_client_list(): array
    {
        $this->virtualserver->clientListReset();

        try {
            $virtualserver_clientlist = $this->virtualserver->clientList(['client_type' => 0]);
        } catch (ServerQueryException) {
            $virtualserver_clientlist = [];
        }

        $clientlist = [];
        foreach ($virtualserver_clientlist as $client) {
            $clientlist[$client->client_database_id] = [
                'CLIENT_DATABASE_ID' => $client->client_database_id,
                'CLIENT_ID' => $client->clid,
                'CLIENT_NICKNAME' => $client->client_nickname,
                'CLIENT_SERVERGROUPS' => $client->client_servergroups,
                'CLIENT_VERSION' => $client->client_version,
                'CLIENT_PLATFORM' => $client->client_platform,
                'CLIENT_COUNTRY' => $client->client_country,
                'CLIENT_CONNECTION_CLIENT_IP' => $client->connection_client_ip,
            ];
        }

        return $clientlist;
    }

    /**
     * Returns a servergroup list with various information.
     */
    public function get_current_servergroup_list(): array
    {
        $this->virtualserver->clientListReset();
        $this->virtualserver->serverGroupListReset();

        /**
         * SERVERGROUP MEMBER ONLINE COUNTER VARIABLE
         */
        try {
            $virtualserver_clientlist = $this->virtualserver->clientList(['client_type' => 0]);
        } catch (ServerQueryException) {
            $virtualserver_clientlist = [];
        }

        $client_servergroup_ids = [];
        foreach ($virtualserver_clientlist as $client) {
            $client_servergroup_ids = array_merge($client_servergroup_ids, explode(',', $client->client_servergroups));
        }

        /**
         * VIRTUALSERVER SERVERGROUPS
         */
        try {
            $virtualserver_servergroups = $this->virtualserver->serverGroupList(['type' => 1]);
        } catch (ServerQueryException) {
            $virtualserver_servergroups = [];
        }

        $servergroups = [];
        foreach ($virtualserver_servergroups as $servergroup) {
            $servergroups['servergroup_'.$servergroup->sgid.'_id'] = $servergroup->sgid;
            $servergroups['servergroup_'.$servergroup->sgid.'_name'] = $servergroup->name;
            try {
                $servergroups['servergroup_'.$servergroup->sgid.'_member_total_count'] = count($this->virtualserver->serverGroupClientList($servergroup->sgid));
            } catch (ServerQueryException) {
                $servergroups['servergroup_'.$servergroup->sgid.'_member_total_count'] = 0;
            }
            $servergroups['servergroup_'.$servergroup->sgid.'_member_online_count'] = (in_array($servergroup->sgid, $client_servergroup_ids)) ? array_count_values($client_servergroup_ids)[$servergroup->sgid] : 0;
        }

        return array_change_key_case($servergroups, CASE_UPPER);
    }

    /**
     * Returns various virtualserver information.
     */
    public function get_current_virtualserver_info(): array
    {
        $virtualserver_info = [];

        /**
         * VIRTUALSERVER NODE INFO
         */
        $virtualserver_info = array_merge($virtualserver_info, $this->virtualserver->getInfo(true, true));

        /**
         * VIRTUALSERVER CONNECTION INFO
         *
         * Some TeamSpeak server versions return every connection property as a
         * separate result row. Flatten those rows so that the property names,
         * rather than their numeric result indexes, become banner variables.
         */
        foreach ($this->virtualserver->connectionInfo() as $key => $value) {
            if (is_array($value)) {
                $virtualserver_info = array_merge($virtualserver_info, $value);

                continue;
            }

            $virtualserver_info[$key] = $value;
        }

        return array_change_key_case($virtualserver_info, CASE_UPPER);
    }

    /**
     * Get client specific information.
     */
    public function get_client_specific_info_from_cache(Instance $instance, string $ip_address): array
    {
        $client_info = [];

        try {
            $client_database_id = Redis::hget('instance_'.$instance->id.'_client_ip_index', $ip_address);

            if (is_null($client_database_id)) {
                $client_database_id = Redis::get('instance_'.$instance->id.'_client_default');
            }

            if (! is_null($client_database_id)) {
                return Redis::hgetall('instance_'.$instance->id.'_client_'.$client_database_id);
            }

            // Compatibility fallback for data written before the per-client cache format.
            $client_variables = Redis::hgetall('instance_'.$instance->id.'_clientlist');
        } catch (RedisException | PredisException) {
            return $client_info;
        }

        $client_variable_key_name = array_search($ip_address, $client_variables);

        if ($client_variable_key_name === false) {
            $random_client_from_cache = array_key_first($client_variables);
            $client_variable_key_name = (! is_null($random_client_from_cache)) ? $random_client_from_cache : '';
        }

        preg_match('/[0-9]+/', $client_variable_key_name, $client_database_id);

        if (empty($client_database_id)) {
            return $client_info;
        }
        $client_database_id = intval($client_database_id[0]);

        $client_variable_array_keys = preg_grep("/\_$client_database_id\_/", array_keys($client_variables));

        if (empty($client_variable_array_keys)) {
            return $client_info;
        }

        foreach ($client_variable_array_keys as $client_variable_key) {
            $client_info[str_replace("_$client_database_id", '', $client_variable_key)] = $client_variables[$client_variable_key];
        }

        return array_change_key_case($client_info, CASE_UPPER);
    }

    /**
     * Get Twitch streamer information.
     */
    public function get_twitch_streamer_information_from_database(?TwitchStreamer $twitch_streamer = null): array
    {
        $streamer_information = [];

        if (is_null(TwitchApi::first()) or is_null($twitch_streamer)) {
            return $streamer_information;
        }

        $streamer_information['twitch_stream_status'] = ($twitch_streamer['is_live']) ? 'LIVE' : 'Offline';
        $streamer_information['twitch_stream_live_since'] = Carbon::parse($twitch_streamer['started_at'])->diffAsCarbonInterval();
        $streamer_information['twitch_stream_game_name'] = $twitch_streamer['game_name'];
        $streamer_information['twitch_stream_title'] = $twitch_streamer['title'];
        $streamer_information['twitch_stream_viewer_count'] = $twitch_streamer['viewer_count'];

        return array_change_key_case($streamer_information, CASE_UPPER);
    }
}
