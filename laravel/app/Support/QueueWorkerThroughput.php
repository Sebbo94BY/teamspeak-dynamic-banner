<?php

namespace App\Support;

use Illuminate\Contracts\Queue\Job;

class QueueWorkerThroughput
{
    /** @var array<string, int> */
    private static array $started_at = [];

    public function started(Job $job): void
    {
        self::$started_at[$this->key($job)] = hrtime(true);
    }

    public function completed(Job $job): void
    {
        $key = $this->key($job);
        $started_at = self::$started_at[$key] ?? null;
        unset(self::$started_at[$key]);

        if (is_null($started_at)) {
            return;
        }

        app(QueueMetrics::class)->record_completed_job(
            $job->getQueue(),
            (int) ((hrtime(true) - $started_at) / 1_000_000),
        );
    }

    private function key(Job $job): string
    {
        return $job->getConnectionName().':'.$job->getJobId();
    }
}
