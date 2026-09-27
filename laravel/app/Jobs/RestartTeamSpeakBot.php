<?php

namespace App\Jobs;

use App\Models\Instance;
use App\Support\TeamSpeakBotLauncher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RestartTeamSpeakBot implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 60;

    public int $uniqueFor = 360;

    public function __construct(
        private readonly int $instanceId,
        private readonly int $scheduledAtTimestamp,
    ) {
    }

    public function uniqueId(): string
    {
        return (string) $this->instanceId;
    }

    public function handle(TeamSpeakBotLauncher $launcher): void
    {
        $instance = Instance::find($this->instanceId);

        // A newer recovery plan or a manual start supersedes this delayed job.
        if (is_null($instance)
            || is_null($instance->bot_restart_scheduled_at)
            || $instance->bot_restart_scheduled_at->getTimestamp() !== $this->scheduledAtTimestamp) {
            return;
        }

        if (! is_null($instance->process)) {
            $instance->saveOperationalState([
                'bot_restart_scheduled_at' => null,
                'bot_restart_reason' => null,
            ]);

            return;
        }

        if ($launcher->start($instance)) {
            $instance->saveOperationalState([
                'bot_restart_scheduled_at' => null,
                'bot_restart_reason' => null,
            ]);
        }
    }
}
