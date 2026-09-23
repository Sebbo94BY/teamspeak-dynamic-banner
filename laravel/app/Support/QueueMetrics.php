<?php

namespace App\Support;

use App\Models\QueueMetricSnapshot;
use Illuminate\Support\Facades\DB;

class QueueMetrics
{
    /**
     * Store one queue state snapshot when the database queue driver is used.
     */
    public function record(): void
    {
        foreach ($this->queues() as $queue) {
            $metrics = $this->current($queue);

            if (is_null($metrics)) {
                continue;
            }

            QueueMetricSnapshot::updateOrCreate(
                ['queue' => $queue, 'recorded_at' => now()->startOfMinute()],
                $metrics,
            );
        }

        QueueMetricSnapshot::where('recorded_at', '<', now()->subDays(30))->delete();
    }

    /**
     * Add a completed job to the current minute's throughput measurement.
     */
    public function record_completed_job(string $queue, int $processing_time_ms): void
    {
        if (! in_array($queue, $this->queues(), true)) {
            return;
        }

        $metrics = $this->current($queue);

        if (is_null($metrics)) {
            return;
        }

        $attributes = ['queue' => $queue, 'recorded_at' => now()->startOfMinute()];
        QueueMetricSnapshot::firstOrCreate($attributes, $metrics);

        QueueMetricSnapshot::where($attributes)->update([
            'processed' => DB::raw('processed + 1'),
            'processing_time_ms' => DB::raw('processing_time_ms + '.max(0, $processing_time_ms)),
        ]);
    }

    /**
     * Return the current database queue state, separated into its task states.
     *
     * @return array<string, int>|null
     */
    public function current(string $queue): ?array
    {
        $queue_connection = config('queue.default');
        $queue_configuration = config('queue.connections.'.$queue_connection);

        if (($queue_configuration['driver'] ?? null) !== 'database') {
            return null;
        }

        $now = now()->timestamp;
        $retry_after = $queue_configuration['retry_after'] ?? 90;
        $queue_jobs = DB::table($queue_configuration['table'])->where('queue', $queue);
        $ready_jobs = (clone $queue_jobs)->whereNull('reserved_at')->where('available_at', '<=', $now);
        $oldest_ready_job = (clone $ready_jobs)->orderBy('created_at')->first(['created_at']);

        return [
            'total' => $queue_jobs->count(),
            'ready' => $ready_jobs->count(),
            'processing' => (clone $queue_jobs)->whereNotNull('reserved_at')->where('reserved_at', '>=', $now - $retry_after)->count(),
            'scheduled' => (clone $queue_jobs)->whereNull('reserved_at')->where('available_at', '>', $now)->count(),
            'stale' => (clone $queue_jobs)->whereNotNull('reserved_at')->where('reserved_at', '<', $now - $retry_after)->count(),
            'oldest_wait_seconds' => $oldest_ready_job ? max(0, $now - $oldest_ready_job->created_at) : 0,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function queues(): array
    {
        $queue_connection = config('queue.default');
        $default_queue = config('queue.connections.'.$queue_connection.'.queue', 'default');

        return config('matomo.enabled') ? [$default_queue, 'matomo'] : [$default_queue];
    }
}
