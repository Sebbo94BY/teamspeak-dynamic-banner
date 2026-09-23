<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\TeamSpeakVirtualserver;
use App\Http\Requests\InstanceAddRequest;
use App\Http\Requests\InstanceDeleteRequest;
use App\Http\Requests\InstanceRestartRequest;
use App\Http\Requests\InstanceStartRequest;
use App\Http\Requests\InstanceStopRequest;
use App\Http\Requests\InstanceUpdateRequest;
use App\Models\Instance;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\ServerQueryException;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\TransportException;

class InstanceController extends Controller
{
    private const BOT_STOP_TIMEOUT_SECONDS = 15;

    private const BOT_STOP_POLL_MICROSECONDS = 100000;

    /**
     * Display the main page.
     */
    public function overview(Request $request): View
    {
        $attention = $request->query('attention');
        $instances = Instance::query();

        if ($attention === 'stopped') {
            $instances->doesntHave('process');
        } else {
            $attention = null;
        }

        $instances = $instances->get();

        $channelListForEachInstance = [];
        foreach ($instances as $instance) {
            try {
                $virtualserver_helper = new TeamSpeakVirtualserver($instance);
                $virtualserver = $virtualserver_helper->get_virtualserver_connection();
                $channel_list = $virtualserver->channelList();
                $channelListForEachInstance[$instance->id]['channel_list'] = $channel_list;
            } catch (TransportException|ServerQueryException|Exception $teamspeak_exception) {
                $channelListForEachInstance[$instance->id]['channel_list'] = [];
                $channelListForEachInstance[$instance->id]['error'] = $teamspeak_exception->getMessage();
            }
        }

        return view('instances')->with([
            'instances' => $instances,
            'channel_list' => $channelListForEachInstance,
            'attention' => $attention,
        ]);
    }

    /**
     * Save a new data set.
     */
    public function save(InstanceAddRequest $request): RedirectResponse
    {
        $instance = new Instance;
        $instance->host = $request->host;
        $instance->voice_port = $request->voice_port;
        $instance->serverquery_port = $request->serverquery_port;
        $instance->is_ssh = $request->has('is_ssh');
        $instance->serverquery_username = $request->serverquery_username;
        $instance->serverquery_password = $request->serverquery_password;
        $instance->client_nickname = $request->client_nickname;

        try {
            $virtualserver_helper = new TeamSpeakVirtualserver($instance);
            $virtualserver = $virtualserver_helper->get_virtualserver_connection();
        } catch (TransportException $transport_exception) {
            return Redirect::route('instances')->withInput($request->all())->with([
                'error' => 'instance-add-error',
                'message' => $transport_exception->getMessage(),
            ]);
        } catch (ServerQueryException $serverquery_exception) {
            return Redirect::route('instances')->withInput($request->all())->with([
                'error' => 'instance-add-error',
                'message' => $serverquery_exception->getMessage(),
            ]);
        }

        $instance->virtualserver_name = $virtualserver->virtualserver_name;

        if (! $instance->save()) {
            return Redirect::route('instances')->withInput($request->all())->with([
                'error' => 'instance-add-error',
                'message' => 'Failed to save the new data set into the database. Please try again.',
            ]);
        }

        return Redirect::route('instances')->with([
            'success' => 'instance-add-successful',
            'message' => 'Successfully added the new instance.',
        ]);
    }

    /**
     * Update an existing data set.
     */
    public function update(InstanceUpdateRequest $request): RedirectResponse
    {
        $instance = Instance::find($request->instance_id);

        $instance->host = $request->host;
        $instance->voice_port = $request->voice_port;
        $instance->serverquery_port = $request->serverquery_port;
        $instance->is_ssh = $request->has('is_ssh');
        $instance->serverquery_username = $request->serverquery_username;
        $instance->serverquery_password = $request->serverquery_password;
        $instance->client_nickname = $request->client_nickname;
        $instance->default_channel_id = $request->default_channel_id;

        try {
            $virtualserver_helper = new TeamSpeakVirtualserver($instance);
            $virtualserver = $virtualserver_helper->get_virtualserver_connection();
        } catch (TransportException $transport_exception) {
            return Redirect::route('instances')->withInput($request->all())->with([
                'error' => 'instance-edit-error',
                'message' => $transport_exception->getMessage(),
            ]);
        } catch (ServerQueryException $serverquery_exception) {
            return Redirect::route('instances')->withInput($request->all())->with([
                'error' => 'instance-edit-error',
                'message' => $serverquery_exception->getMessage(),
            ]);
        }

        $instance->virtualserver_name = $virtualserver->virtualserver_name;
        $instance->autostart_enabled = $request->has('autostart_enabled');

        if (! $instance->save()) {
            return Redirect::route('instances')
                ->withInput($request->all())
                ->with([
                    'error' => 'instance-edit-error',
                    'message' => 'Failed to update the database entry. Please try again.',
                ]);
        }

        return Redirect::route('instances')->with([
            'success' => 'instance-edit-successful',
            'message' => 'Successfully updated the instance.',
        ]);
    }

    /**
     * Delete the instance.
     */
    public function delete(InstanceDeleteRequest $request): RedirectResponse
    {
        $instance = Instance::find($request->instance_id);

        if (! $instance->delete()) {
            return redirect(route('instances'), 302)->with([
                'error' => 'instance-delete-error',
                'message' => 'Failed to delete the instance from the database. Please try again.',
            ]);
        }

        return redirect(route('instances'), 302)->with([
            'success' => 'instance-delete-successful',
            'message' => 'Successfully deleted the instance.',
        ]);
    }

    /**
     * Starts the bot for the instance.
     */
    public function start(InstanceStartRequest $request): RedirectResponse
    {
        $instance = Instance::find($request->instance_id);

        $process = Process::start('php '.base_path()."/artisan instance:start-teamspeak-bot $instance->id --background");

        if (! $process->running()) {
            return Redirect::route('instances')->with([
                'error' => 'instance-start-error',
                'message' => 'Failed to start the instance.',
            ]);
        }

        return Redirect::route('instances')->with([
            'success' => 'instance-start-successful',
            'message' => 'Successfully started the instance. Refreshing status in 30 seconds...',
            'refresh_status_after_seconds' => 30,
        ]);
    }

    /**
     * Stops the bot for the instance.
     */
    public function stop(InstanceStopRequest $request): RedirectResponse
    {
        $instance = Instance::find($request->instance_id);

        $process_id = $instance->process->process_id;
        $process = Process::run('php '.base_path()."/artisan process:send-signal SIGTERM $process_id");

        if (! $process->successful()) {
            return Redirect::route('instances')->with([
                'error' => 'instance-stop-error',
                'message' => 'Failed to stop the instance.',
            ]);
        }

        if (! $this->wait_for_bot_to_stop($process_id)) {
            return Redirect::route('instances')->with([
                'error' => 'instance-stop-error',
                'message' => 'The bot did not stop within the expected time. Please try again.',
            ]);
        }

        if (! $instance->process->delete()) {
            return Redirect::route('instances')->with([
                'error' => 'instance-process-error',
                'message' => 'Failed to clean up the stopped process from the database.',
            ]);
        }

        return Redirect::route('instances')->with([
            'success' => 'instance-stop-successful',
            'message' => 'Successfully stopped the instance.',
        ]);
    }

    /**
     * Restarts the bot for the instance.
     */
    public function restart(InstanceRestartRequest $request): RedirectResponse
    {
        $instance = Instance::findOrFail($request->instance_id);

        $process_id = $instance->process->process_id;
        $process = Process::run('php '.base_path()."/artisan process:send-signal SIGTERM $process_id");

        if (! $process->successful()) {
            return Redirect::route('instances')->with([
                'error' => 'instance-stop-error',
                'message' => 'Failed to stop the instance.',
            ]);
        }

        if (! $this->wait_for_bot_to_stop($process_id)) {
            return Redirect::route('instances')->with([
                'error' => 'instance-stop-error',
                'message' => 'The bot did not stop within the expected time. Please try again.',
            ]);
        }

        if (! $instance->process->delete()) {
            return Redirect::route('instances')->with([
                'error' => 'instance-process-error',
                'message' => 'Failed to clean up the stopped process from the database.',
            ]);
        }

        $process = Process::start('php '.base_path()."/artisan instance:start-teamspeak-bot $instance->id --background");

        if (! $process->running()) {
            return Redirect::route('instances')->with([
                'error' => 'instance-restart-error',
                'message' => 'Failed to restart the instance.',
            ]);
        }

        return Redirect::route('instances')->with([
            'success' => 'instance-restart-successful',
            'message' => 'Successfully restarted the instance. Refreshing status in 30 seconds...',
            'refresh_status_after_seconds' => 30,
        ]);
    }

    /** Wait until the signalled bot has actually exited before starting a replacement. */
    protected function wait_for_bot_to_stop(int $process_id): bool
    {
        $deadline = microtime(true) + self::BOT_STOP_TIMEOUT_SECONDS;

        while (file_exists("/proc/$process_id")) {
            if (microtime(true) >= $deadline) {
                return false;
            }

            usleep(self::BOT_STOP_POLL_MICROSECONDS);
        }

        return true;
    }
}
