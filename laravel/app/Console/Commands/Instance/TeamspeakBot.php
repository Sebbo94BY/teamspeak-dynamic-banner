<?php

namespace App\Console\Commands\Instance;

use App\Http\Controllers\Helpers\BannerVariableController;
use App\Http\Controllers\Helpers\TeamSpeakVirtualserver;
use App\Models\Instance;
use App\Models\InstanceProcess;
use App\Support\ThrottledErrorLogger;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use PlanetTeamSpeak\TeamSpeak3Framework\Adapter\ServerQuery;
use PlanetTeamSpeak\TeamSpeak3Framework\Adapter\ServerQuery\Event;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\AdapterException;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\ServerQueryException;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\TransportException;
use PlanetTeamSpeak\TeamSpeak3Framework\Helper\Signal;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Host;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;
use Predis\Connection\ConnectionException;
use RedisException;

class TeamspeakBot extends Command
{
    // Banner time variables have minute precision, but must be refreshed soon after a minute change.
    private const DATETIME_REFRESH_INTERVAL_SECONDS = 15;

    // Virtual server statistics change continuously, unlike server group metadata.
    private const VIRTUALSERVER_REFRESH_INTERVAL_SECONDS = 60;

    private const SERVERGROUP_REFRESH_INTERVAL_SECONDS = 60 * 10;

    private const STARTUP_REFRESH_ATTEMPTS = 3;

    // TeamSpeak may accept the query connection before all connection metrics are available.
    private const STARTUP_REFRESH_RETRY_DELAY_SECONDS = 10;

    private const INITIAL_CONNECTION_ATTEMPTS = 3;

    private const INITIAL_CONNECTION_RETRY_DELAY_SECONDS = 1;

    private const CACHE_REFRESH_ERROR_LOG_COOLDOWN_SECONDS = 60 * 5;

    private const DATETIME_CACHE_TTL_SECONDS = 60 * 5;

    private const SERVERGROUP_CACHE_TTL_SECONDS = 60 * 30;

    private const VIRTUALSERVER_CACHE_TTL_SECONDS = 60 * 5;

    // Client data is event-driven, rather than polled; retain it across reconnects.
    private const CLIENT_CACHE_TTL_SECONDS = 60 * 60 * 12;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instance:start-teamspeak-bot 
        {instance_id : The database ID of the instance.}
        {--background : Actually only switches the command output to file logging.}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts a TeamSpeak bot for a specific instance.';

    /**
     * The instance model.
     */
    protected Instance $instance;

    /**
     * The instance process model.
     */
    protected InstanceProcess $instance_process;

    /**
     * The TeamSpeak virtualserver connection.
     */
    protected ?Server $virtualserver;

    /**
     * A helper variable for properly stopping this endless running script.
     */
    protected bool $keep_running = true;

    /** @var array<string, bool> The outcome of the most recent complete cache refresh. */
    protected array $cache_refresh_status = [];

    /** Whether a TeamSpeak request lost its connection during the current refresh. */
    protected bool $teamspeak_connection_lost = false;

    /** Prevent nested TeamSpeak requests from timeout and event callbacks. */
    protected bool $teamspeak_refresh_in_progress = false;

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (isset($this->virtualserver)) {
            $this->virtualserver->request('quit');
            unset($this->virtualserver);
        }
    }

    /**
     * Print the message or log it.
     */
    protected function message(string $log_level, string $message)
    {
        switch (strtoupper($log_level)) {
            case 'INFO':
                (boolval($this->option('background'))) ? Log::info($message) : $this->info($message);
                break;
            case 'WARNING':
                (boolval($this->option('background'))) ? Log::warning($message) : $this->warn($message);
                break;
            case 'ERROR':
                (boolval($this->option('background'))) ? Log::error($message) : $this->error($message);
                break;
            default:
                (boolval($this->option('background'))) ? Log::debug($message) : $this->warn($message);
                break;
        }
    }

    /**
     * Callback method for 'serverqueryWaitTimeout' signals.
     *
     * @param  int  $idle_seconds
     * @param  ServerQuery  $serverquery
     * @return void
     * @throws ServerQueryException
     * @throws AdapterException
     */
    public function onWaitTimeout(int $idle_seconds, ServerQuery $serverquery)
    {
        // Just for debugging and development purposes
        // Print (or log) every 30 seconds the current idle time of the bot connection.
        if ($idle_seconds % 30 == 0) {
            $this->message('DEBUG', "No reply from the server for $idle_seconds seconds.");
        }

        if (! $this->keep_running) {
            // unregister from all events
            $this->virtualserver->notifyUnregister();

            // Finally exit the script.
            // Otherwise the Artisan command hangs forever.
            exit(0);
        }

        // waitForReadyRead() emits this callback while any non-blocking query
        // is waiting for its reply. Starting another query here would mix both
        // replies and can make a client list look like a server group list.
        if ($this->teamspeak_refresh_in_progress) {
            return;
        }

        // If the timestamp on the last query is more than 300 seconds (5 minutes) in the past, send 'keepalive'
        // 'keepalive' command is just server query command 'clientupdate' which does nothing without properties. So nothing changes.
        if ($serverquery->getQueryLastTimestamp() < time() - 260) {
            $this->message('DEBUG', 'Sending keep-alive.');
            $serverquery->request('clientupdate');
        }

        // Update minute-precision time values frequently enough to avoid stale values after a minute change.
        if ($idle_seconds % self::DATETIME_REFRESH_INTERVAL_SECONDS == 0) {
            call_user_func($this->updateDatetime(...));
        }

        // Virtual server statistics change continuously.
        if ($idle_seconds % self::VIRTUALSERVER_REFRESH_INTERVAL_SECONDS == 0) {
            call_user_func($this->updateVirtualserverInfo(...));
        }

        // Server group metadata is refreshed periodically and immediately on client events.
        if ($idle_seconds % self::SERVERGROUP_REFRESH_INTERVAL_SECONDS == 0) {
            call_user_func($this->updateServergroupList(...));
        }
    }

    /**
     * Callback method for 'notifyEvent' signals.
     *
     * @param  Event  $event
     * @param  Host  $host
     * @return void
     */
    public function onEvent(Event $event, Host $host)
    {
        $this->message('DEBUG', 'Received the following event: '.json_encode($event->getType()));

        if (! $this->keep_running || $this->teamspeak_refresh_in_progress) {
            return;
        }

        // Those `client*view` events also include events for kicked and banned clients.
        if (in_array($event->getType(), ['cliententerview', 'clientleftview'])) {
            call_user_func($this->updateClientList(...));
            call_user_func($this->updateServergroupList(...));
            call_user_func($this->updateVirtualserverInfo(...));
        }
    }

    /**
     * Inserts and updates the given $data in the $redis_key for a duration of $ttl seconds.
     *
     * @param array $data
     * @param string $redis_key
     * @param int $ttl
     * @return bool
     */
    protected function update_data_in_redis(array $data, string $redis_key, int $ttl = 60): bool
    {
        if (count($data) == 0) {
            return false;
        }

        try {
            // Write to a separate key first and atomically replace the old hash only
            // after it is complete. Readers must never observe a partly refreshed cache.
            $staging_key = $redis_key.':staging:'.bin2hex(random_bytes(8));
            Redis::hmset($staging_key, $this->normalize_data_for_redis($data));
            Redis::expire($staging_key, $ttl);
            Redis::rename($staging_key, $redis_key);

            return true;
        } catch (RedisException | ConnectionException | Exception) {
            // Do nothing when the Redis
            // - should not answer within the expected timeout time.
            // - should fail to expire / save data.
            // The next iteration will retry it.

            return false;
        }
    }

    /**
     * Converts framework value objects into Redis-compatible scalar values.
     */
    protected function normalize_data_for_redis(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($value instanceof \Stringable) {
                $data[$key] = (string) $value;
            } elseif (! is_scalar($value)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Caches the current datetime in various formats.
     */
    public function updateDatetime()
    {
        if (! $this->keep_running) {
            return false;
        }

        $this->message('DEBUG', 'Caching the current datetime in various formats...');

        $banner_variable_helper = new BannerVariableController($this->virtualserver);

        return $this->update_data_in_redis(
            $banner_variable_helper->get_current_time_data(),
            'instance_'.$this->instance->id.'_datetime',
            self::DATETIME_CACHE_TTL_SECONDS
        );
    }

    /**
     * Fetches current client list from the TeamSpeak.
     */
    public function updateClientList()
    {
        if (! $this->keep_running || $this->teamspeak_refresh_in_progress) {
            return false;
        }

        $this->message('DEBUG', 'Caching the current client list...');

        $this->teamspeak_refresh_in_progress = true;
        try {
            $this->teamspeak_connection_lost = false;
            $banner_variable_helper = new BannerVariableController($this->virtualserver);
            $clients = $banner_variable_helper->get_current_client_list();
            $cache_ttl = self::CLIENT_CACHE_TTL_SECONDS;
            $ip_index_key = 'instance_'.$this->instance->id.'_client_ip_index';
            $ip_index = [];

            foreach ($clients as $client_database_id => $client) {
                $client_key = 'instance_'.$this->instance->id.'_client_'.$client_database_id;
                $client = $this->normalize_data_for_redis($client);
                Redis::hmset($client_key, $client);
                Redis::expire($client_key, $cache_ttl);
                $ip_index[$client['CLIENT_CONNECTION_CLIENT_IP']] = $client_database_id;
            }

            if ($clients !== []) {
                // The index is read alongside the client hashes. Replacing it atomically
                // avoids a momentary empty client selection during a refresh.
                $ip_index_cached = $this->update_data_in_redis($ip_index, $ip_index_key, $cache_ttl);
                Redis::set('instance_'.$this->instance->id.'_client_default', array_key_first($clients), 'EX', $cache_ttl);

                if (! $ip_index_cached) {
                    return false;
                }
            } else {
                Redis::del($ip_index_key);
                Redis::del('instance_'.$this->instance->id.'_client_default');
            }

            return true;
        } catch (TransportException $exception) {
            $this->teamspeak_connection_lost = true;
            $this->log_cache_refresh_failure('client', $exception);

            throw $exception;
        } catch (RedisException | ConnectionException | Exception) {
            $this->log_cache_refresh_failure('client');
        } finally {
            $this->teamspeak_refresh_in_progress = false;
        }

        return false;
    }

    /**
     * Fetches current servergroup list incl. member online counter from the TeamSpeak.
     */
    public function updateServergroupList()
    {
        if (! $this->keep_running || $this->teamspeak_refresh_in_progress) {
            return false;
        }

        $this->message('DEBUG', 'Caching the current servergroup list...');

        $banner_variable_helper = new BannerVariableController($this->virtualserver);
        $failure_logged = false;
        $this->teamspeak_refresh_in_progress = true;

        try {
            $this->teamspeak_connection_lost = false;
            $cache_refreshed = $this->update_data_in_redis(
                $banner_variable_helper->get_current_servergroup_list(),
                'instance_'.$this->instance->id.'_servergrouplist',
                self::SERVERGROUP_CACHE_TTL_SECONDS
            );
        } catch (TransportException $exception) {
            $cache_refreshed = false;
            $this->teamspeak_connection_lost = true;
            $this->log_cache_refresh_failure('server group', $exception);
            $failure_logged = true;

            throw $exception;
        } catch (\Throwable $exception) {
            $cache_refreshed = false;
            $this->log_cache_refresh_failure('server group', $exception);
            $failure_logged = true;
        } finally {
            $this->teamspeak_refresh_in_progress = false;
        }

        if (! $cache_refreshed && ! $failure_logged) {
            $this->log_cache_refresh_failure('server group');
        }

        return $cache_refreshed;
    }

    /**
     * Fetches current virtualserver info from the TeamSpeak.
     */
    public function updateVirtualserverInfo()
    {
        if (! $this->keep_running || $this->teamspeak_refresh_in_progress) {
            return false;
        }

        $this->message('DEBUG', 'Caching the current virtualserver info...');

        $banner_variable_helper = new BannerVariableController($this->virtualserver);

        $this->teamspeak_refresh_in_progress = true;
        try {
            $this->teamspeak_connection_lost = false;

            return $this->update_data_in_redis(
                $banner_variable_helper->get_current_virtualserver_info(),
                'instance_'.$this->instance->id.'_virtualserver_info',
                self::VIRTUALSERVER_CACHE_TTL_SECONDS
            );
        } catch (TransportException $exception) {
            $this->teamspeak_connection_lost = true;
            $this->log_cache_refresh_failure('virtual server', $exception);

            throw $exception;
        } catch (\Throwable $exception) {
            $this->log_cache_refresh_failure('virtual server', $exception);
        } finally {
            $this->teamspeak_refresh_in_progress = false;
        }

        return false;
    }

    /**
     * Starts the actual bot for the instance.
     */
    protected function start_bot()
    {
        $this->message('INFO', 'Starting TeamSpeak bot instance: '.$this->instance->virtualserver_name);

        $virtualserver_helper = $this->create_virtualserver_helper();

        if (! $this->connect_to_virtualserver_with_retries($virtualserver_helper)) {
            $this->message('ERROR', "Could not connect to the host `$this->instance->host` after ".self::INITIAL_CONNECTION_ATTEMPTS.' attempts.');

            $this->cleanup_instance_process_id();

            return;
        }

        if (! $this->keep_running) {
            return;
        }

        $this->refresh_cached_data();
        $this->retry_cache_refresh_during_startup($virtualserver_helper);

        if (! $this->keep_running) {
            return;
        }

        // register for server events
        $this->virtualserver->notifyRegister('server');

        // register a callback for notifyEvent events
        Signal::getInstance()->subscribe('notifyEvent', $this->onEvent(...));

        // register a callback for serverqueryWaitTimeout events
        Signal::getInstance()->subscribe('serverqueryWaitTimeout', $this->onWaitTimeout(...));

        // wait for events
        while ($this->keep_running) {
            try {
                $this->virtualserver->getAdapter()->wait();
            } catch (TransportException $transport_exception) {
                if (! $this->keep_running) {
                    break;
                }

                $this->message('WARNING', "Connection to `{$this->instance->host}` was lost. Reconnecting...");
                $this->reconnect_to_virtualserver($virtualserver_helper);
            }
        }
    }

    /**
     * Creates the virtual server connector.
     */
    protected function create_virtualserver_helper(): TeamSpeakVirtualserver
    {
        return new TeamSpeakVirtualserver($this->instance);
    }

    /**
     * Connects a new bot with short delays so a just-stopped query client can
     * finish disconnecting before its replacement uses the same nickname.
     */
    protected function connect_to_virtualserver_with_retries(TeamSpeakVirtualserver $virtualserver_helper): bool
    {
        for ($attempt = 1; $this->keep_running && $attempt <= self::INITIAL_CONNECTION_ATTEMPTS; $attempt++) {
            try {
                $this->virtualserver = $virtualserver_helper->get_virtualserver_connection(false);

                return true;
            } catch (TransportException | ServerQueryException | Exception $connection_exception) {
                $this->message('WARNING', "TeamSpeak connection attempt $attempt/".self::INITIAL_CONNECTION_ATTEMPTS." failed: {$connection_exception->getMessage()}");

                if ($attempt < self::INITIAL_CONNECTION_ATTEMPTS) {
                    $this->wait_before_initial_connection_retry();
                }
            }
        }

        return false;
    }

    protected function wait_before_initial_connection_retry(): void
    {
        sleep(self::INITIAL_CONNECTION_RETRY_DELAY_SECONDS);
    }

    /**
     * Reconnects the bot and restores its event subscriptions and cached data.
     */
    protected function reconnect_to_virtualserver(TeamSpeakVirtualserver $virtualserver_helper): bool
    {
        if (! $this->keep_running) {
            return false;
        }

        try {
            $this->virtualserver = $virtualserver_helper->get_virtualserver_connection(false);
            $this->refresh_cached_data();
            $this->virtualserver->notifyRegister('server');
        } catch (TransportException | ServerQueryException | Exception $connection_exception) {
            $this->message('ERROR', "Reconnect to `{$this->instance->host}` failed: ".$connection_exception->getMessage());
            $this->wait_before_reconnect();

            return false;
        }

        return true;
    }

    /**
     * Limits reconnect attempts after a failed connection attempt.
     */
    protected function wait_before_reconnect(): void
    {
        sleep(5);
    }

    /**
     * Updates all cached TeamSpeak data after connecting or reconnecting.
     */
    protected function refresh_cached_data()
    {
        if (! $this->keep_running) {
            return false;
        }

        // Update all data once immediately, when the bot initially starts
        $this->teamspeak_connection_lost = false;
        $this->cache_refresh_status = ['datetime' => (bool) $this->updateDatetime()];

        try {
            $this->cache_refresh_status['clients'] = (bool) $this->updateClientList();
            $this->cache_refresh_status['servergroups'] = (bool) $this->updateServergroupList();
            $this->cache_refresh_status['virtualserver'] = (bool) $this->updateVirtualserverInfo();
        } catch (TransportException) {
            // Startup retries reconnect before attempting a complete refresh again.
            $this->teamspeak_connection_lost = true;
            $this->cache_refresh_status += [
                'clients' => false,
                'servergroups' => false,
                'virtualserver' => false,
            ];
        }

        return ! in_array(false, $this->cache_refresh_status, true);
    }

    /**
     * Retries the complete cache refresh during startup. Regular refreshes
     * rely on their TTL reserve, but startup must populate every cache source.
     */
    protected function retry_cache_refresh_during_startup(?TeamSpeakVirtualserver $virtualserver_helper = null): void
    {
        for ($attempt = 2; $this->keep_running && in_array(false, $this->cache_refresh_status, true) && $attempt <= self::STARTUP_REFRESH_ATTEMPTS; $attempt++) {
            $this->message('WARNING', "Retrying the complete TeamSpeak cache refresh (attempt $attempt/".self::STARTUP_REFRESH_ATTEMPTS.').');
            $this->wait_before_startup_refresh_retry();

            if (! $this->keep_running) {
                return;
            }

            if ($this->teamspeak_connection_lost) {
                if (is_null($virtualserver_helper)) {
                    continue;
                }

                try {
                    $this->virtualserver = $virtualserver_helper->get_virtualserver_connection(false);
                    $this->teamspeak_connection_lost = false;
                } catch (TransportException | ServerQueryException $connection_exception) {
                    $this->message('WARNING', 'Failed to reconnect before retrying the TeamSpeak cache: '.$connection_exception->getMessage());

                    continue;
                }
            }

            $this->refresh_cached_data();
        }
    }

    protected function wait_before_startup_refresh_retry(): void
    {
        sleep(self::STARTUP_REFRESH_RETRY_DELAY_SECONDS);
    }

    /**
     * Logs repeated regular-refresh failures at most once per cooldown period.
     * Startup retries are logged separately and are capped at two per startup.
     */
    protected function log_cache_refresh_failure(string $source, ?\Throwable $exception = null): void
    {
        if (! $this->keep_running) {
            return;
        }

        $message = "TeamSpeak $source cache refresh failed for instance {$this->instance->id}";
        if (! is_null($exception)) {
            $message .= ': '.$exception->getMessage();
        }

        (new ThrottledErrorLogger)->log($message, self::CACHE_REFRESH_ERROR_LOG_COOLDOWN_SECONDS);
    }

    /**
     * Removes the process ID from the database, when the process exited.
     */
    protected function cleanup_instance_process_id(): void
    {
        if (! $this->instance_process->delete()) {
            $this->message('WARNING', 'Failed to delete the process ID from the database.');
        }
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $process_id = getmypid();

        $this->message('INFO', "My Process ID (PID) is $process_id.");

        // setup signal handlers
        $this->message('INFO', 'Setting up signal handlers.');
        $this->trap([
            SIGINT,  // Ctrl+C signals
            SIGQUIT, // Similar to SIGINT
            SIGTERM, // shutdown signals
            SIGHUP,  // "hang-up" signals (e. g. user's terminal disconnected)
        ], function (int $signal) {
            $this->message('INFO', "Received the PCNTL signal number `$signal`. Stopping the bot.");

            $this->keep_running = false;

            $this->message('INFO', 'Stopping the bot. Please wait a few seconds.');

            $this->cleanup_instance_process_id();

            $this->message('INFO', 'Successfully stopped the bot.');

            // A signal can arrive in the middle of a TeamSpeak request. Returning
            // from the handler would let that request continue and potentially mix
            // its reply with the replacement bot's requests.
            exit(0);
        });

        $instance_id = intval($this->argument('instance_id'));

        try {
            $this->instance = Instance::findOrFail($instance_id);
        } catch (ModelNotFoundException) {
            $this->message('ERROR', "Could not find any instance with the ID `$instance_id`.");

            return;
        }

        $command = 'php artisan instance:start-teamspeak-bot '.$this->instance->id;

        $existing_instance_process = InstanceProcess::where([
            ['instance_id', '=', $instance_id],
            ['command', '=', $command],
        ])->first();

        if (! is_null($existing_instance_process)) {
            $this->message('ERROR', "There is already a bot with the process ID (PID) `$existing_instance_process->process_id` for this instance running. Aborting.");
            $this->message('WARNING', 'If this is a dead process ID (PID), a schedule will remove it within the next minute from the database.');
            $this->message('WARNING', 'Alternatively, you can run the following Artisan command to remove it immediately: php artisan process:cleanup-dead-pids');

            return;
        }

        $this->instance_process = InstanceProcess::create([
            'instance_id' => $this->instance->id,
            'command' => $command,
            'process_id' => $process_id,
        ]);

        if (! $this->instance_process instanceof InstanceProcess) {
            $this->message('ERROR', 'Failed to save the process ID to the database, so that it can be stopped later by the UI.');

            return;
        }

        $this->start_bot();
    }
}
