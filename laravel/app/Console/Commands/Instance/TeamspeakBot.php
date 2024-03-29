<?php

namespace App\Console\Commands\Instance;

use App\Http\Controllers\Helpers\BannerVariableController;
use App\Http\Controllers\Helpers\TeamSpeakVirtualserver;
use App\Models\Instance;
use App\Models\InstanceProcess;
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

class TeamspeakBot extends Command
{
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

        // If the timestamp on the last query is more than 300 seconds (5 minutes) in the past, send 'keepalive'
        // 'keepalive' command is just server query command 'clientupdate' which does nothing without properties. So nothing changes.
        if ($serverquery->getQueryLastTimestamp() < time() - 260) {
            $this->message('DEBUG', 'Sending keep-alive.');
            $serverquery->request('clientupdate');
        }

        // Update data every 15 seconds
        if ($idle_seconds % 15 == 0) {
            call_user_func($this->updateDatetime(...));
        }

        // Update data every minute
        if ($idle_seconds % 60 == 0) {
            call_user_func($this->updateServergroupList(...));
            call_user_func($this->updateVirtualserverInfo(...));
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
     * @return void
     */
    protected function update_data_in_redis(array $data, string $redis_key, int $ttl = 60)
    {
        if (count($data) == 0) {
            return;
        }

        try {
            Redis::expire($redis_key, -2);
            Redis::hmset($redis_key, $data);
            Redis::expire($redis_key, $ttl);
        } catch (ConnectionException | Exception) {
            // Do nothing when the Redis
            // - should not answer within the expected timeout time.
            // - should fail to expire / save data.
            // The next iteration will retry it.
        }
    }

    /**
     * Caches the current datetime in various formats.
     */
    public function updateDatetime()
    {
        $this->message('DEBUG', 'Caching the current datetime in various formats...');

        $banner_variable_helper = new BannerVariableController($this->virtualserver);

        $this->update_data_in_redis(
            $banner_variable_helper->get_current_time_data(),
            'instance_'.$this->instance->id.'_datetime',
            60 * 5 // 5 minutes
        );
    }

    /**
     * Fetches current client list from the TeamSpeak.
     */
    public function updateClientList()
    {
        $this->message('DEBUG', 'Caching the current client list...');

        $banner_variable_helper = new BannerVariableController($this->virtualserver);

        $this->update_data_in_redis(
            $banner_variable_helper->get_current_client_list(),
            'instance_'.$this->instance->id.'_clientlist',
            60 * 60 * 12 // 12 hours
        );
    }

    /**
     * Fetches current servergroup list incl. member online counter from the TeamSpeak.
     */
    public function updateServergroupList()
    {
        $this->message('DEBUG', 'Caching the current servergroup list...');

        $banner_variable_helper = new BannerVariableController($this->virtualserver);

        $this->update_data_in_redis(
            $banner_variable_helper->get_current_servergroup_list(),
            'instance_'.$this->instance->id.'_servergrouplist',
            60 * 1.25 // 1 minute, 15 seconds
        );
    }

    /**
     * Fetches current virtualserver info from the TeamSpeak.
     */
    public function updateVirtualserverInfo()
    {
        $this->message('DEBUG', 'Caching the current virtualserver info...');

        $banner_variable_helper = new BannerVariableController($this->virtualserver);

        $this->update_data_in_redis(
            $banner_variable_helper->get_current_virtualserver_info(),
            'instance_'.$this->instance->id.'_virtualserver_info',
            60 * 15 // 15 minutes
        );
    }

    /**
     * Starts the actual bot for the instance.
     */
    protected function start_bot()
    {
        $this->message('INFO', 'Starting TeamSpeak bot instance: '.$this->instance->virtualserver_name);

        $virtualserver_helper = new TeamSpeakVirtualserver($this->instance);

        try {
            $this->virtualserver = $virtualserver_helper->get_virtualserver_connection(false);
        } catch (TransportException $transport_exception) {
            $this->message('ERROR', "Could not connect to the host `$this->instance->host`: ".$transport_exception->getMessage());

            $this->cleanup_instance_process_id();

            return;
        } catch (ServerQueryException $serverquery_exception) {
            $this->message('ERROR', 'ServerQuery command failed: '.$serverquery_exception->getMessage().' (Error #'.$serverquery_exception->getCode().')');

            $this->cleanup_instance_process_id();

            return;
        }

        // Update all data once immediately, when the bot initially starts
        $this->updateDatetime();
        $this->updateClientList();
        $this->updateServergroupList();
        $this->updateVirtualserverInfo();

        // register for server events
        $this->virtualserver->notifyRegister('server');

        // register a callback for notifyEvent events
        Signal::getInstance()->subscribe('notifyEvent', $this->onEvent(...));

        // register a callback for serverqueryWaitTimeout events
        Signal::getInstance()->subscribe('serverqueryWaitTimeout', $this->onWaitTimeout(...));

        // wait for events
        while ($this->keep_running) {
            $this->virtualserver->getAdapter()->wait();
        }
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
