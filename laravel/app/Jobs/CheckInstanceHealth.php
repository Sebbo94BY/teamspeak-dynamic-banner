<?php

namespace App\Jobs;

use App\Models\Instance;
use App\Support\InstanceHealthCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckInstanceHealth implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** Do not leave a stalled TeamSpeak check in the queue indefinitely. */
    public int $timeout = 60;

    /** A pending check for the same instance is sufficient. */
    public int $uniqueFor = 300;

    public function __construct(private readonly int $instanceId)
    {
    }

    public function uniqueId(): string
    {
        return (string) $this->instanceId;
    }

    public function handle(InstanceHealthCheck $healthCheck): void
    {
        $instance = Instance::find($this->instanceId);

        if (! is_null($instance)) {
            $healthCheck->checkAndStore($instance);
        }
    }
}
