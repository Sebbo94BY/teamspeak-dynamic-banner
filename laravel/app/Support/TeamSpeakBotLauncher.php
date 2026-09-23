<?php

namespace App\Support;

use App\Models\Instance;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class TeamSpeakBotLauncher
{
    /**
     * Start the long-running TeamSpeak bot independently from the current PHP process.
     */
    public function start(Instance $instance): bool
    {
        Log::info("Starting TeamSpeak bot for instance $instance->id.");

        // Process::start() owns the spawned process and terminates it when the
        // request ends. nohup and the background shell operator detach the bot
        // before the short-lived launcher process exits.
        $process = Process::run(sprintf(
            'nohup php %s instance:start-teamspeak-bot %d --background > /dev/null 2>&1 &',
            escapeshellarg(base_path('artisan')),
            $instance->id,
        ));

        if ($process->successful()) {
            return true;
        }

        Log::error("Failed to launch TeamSpeak bot for instance $instance->id: {$process->errorOutput()}");

        return false;
    }
}
