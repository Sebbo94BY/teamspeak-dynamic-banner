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
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\ServerQueryException;
use PlanetTeamSpeak\TeamSpeak3Framework\Exception\TransportException;

class InstanceController extends Controller
{
    /**
     * Display the main page.
     */
    public function overview(): View
    {
        $instances = Instance::all();

        $channelListForEachInstance = [];
        foreach ($instances as $instance) {
            try {
                $virtualserver_helper = new TeamSpeakVirtualserver($instance);
                $virtualserver = $virtualserver_helper->get_virtualserver_connection();
                $channel_list = $virtualserver->channelList();
                $channelListForEachInstance[$instance->id] = $channel_list;
            } catch (ServerQueryException $serverquery_exception) {
                $channelListForEachInstance[$instance->id] = ['error' => $serverquery_exception->getMessage()];
            }
        }

        return view('instances')->with([
            'instances' => $instances,
            'channel_list' => $channelListForEachInstance,
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
        $instance->serverquery_username = $request->serverquery_username;
        $instance->serverquery_password = $request->serverquery_password;
        $instance->client_nickname = $request->client_nickname;

        // Try SSH first and fallback to RAW
        $instance->is_ssh = true;

        try {
            $virtualserver_helper = new TeamSpeakVirtualserver($instance);
            $virtualserver = $virtualserver_helper->get_virtualserver_connection();
        } catch (TransportException) {
            try {
                $instance->is_ssh = false;

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
        $instance->serverquery_username = $request->serverquery_username;
        $instance->serverquery_password = $request->serverquery_password;
        $instance->client_nickname = $request->client_nickname;
        $instance->default_channel_id = $request->default_channel_id;

        // Try SSH first and fallback to RAW
        try {
            $instance->is_ssh = true;

            $virtualserver_helper = new TeamSpeakVirtualserver($instance);
            $virtualserver = $virtualserver_helper->get_virtualserver_connection();
        } catch (TransportException) {
            try {
                $instance->is_ssh = false;

                $virtualserver_helper = new TeamSpeakVirtualserver($instance);
                $virtualserver = $virtualserver_helper->get_virtualserver_connection();
            } catch (TransportException $transport_exception) {
                return Redirect::route('instance.edit', ['instance_id' => $instance->id])->withInput($request->all())->with([
                    'error' => 'instance-edit-error',
                    'message' => $transport_exception->getMessage(),
                ]);
            } catch (ServerQueryException $serverquery_exception) {
                return Redirect::route('instance.edit', ['instance_id' => $instance->id])->withInput($request->all())->with([
                    'error' => 'instance-edit-error',
                    'message' => $serverquery_exception->getMessage(),
                ]);
            }
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
            'message' => 'Successfully started the instance. Refreshing status in 5 seconds...',
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
            'message' => 'Successfully restarted the instance. Refreshing status in 5 seconds...',
        ]);
    }
}
