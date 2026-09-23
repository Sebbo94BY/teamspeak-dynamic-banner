<?php

namespace App\Support;

use Illuminate\Support\Facades\Redis;
use Predis\PredisException;
use RedisException;

class QueueWorkerHeartbeat
{
    private const TTL_SECONDS = 90;

    /**
     * Record that this worker is alive for each queue it listens to.
     */
    public function report(string $queues): void
    {
        $worker = (gethostname() ?: 'unknown-host').':'.getmypid();

        try {
            foreach (explode(',', $queues) as $queue) {
                $queue = trim($queue);

                if ($queue === '') {
                    continue;
                }

                Redis::hset($this->key($queue), $worker, now()->timestamp);
                Redis::expire($this->key($queue), self::TTL_SECONDS);
            }
        } catch (RedisException|PredisException) {
            // The Redis status check reports connection failures separately.
        }
    }

    /**
     * Count workers that have reported during the heartbeat interval.
     */
    public function active_workers(string $queue): ?int
    {
        try {
            $heartbeats = Redis::hgetall($this->key($queue));
        } catch (RedisException|PredisException) {
            return null;
        }

        $minimum_timestamp = now()->subSeconds(self::TTL_SECONDS)->timestamp;

        return count(array_filter($heartbeats, fn ($timestamp) => (int) $timestamp >= $minimum_timestamp));
    }

    private function key(string $queue): string
    {
        return 'queue_worker_heartbeats:'.$queue;
    }
}
