<?php

namespace Tests\Feature\Support;

use App\Support\QueueWorkerHeartbeat;
use Illuminate\Support\Facades\Redis;
use RedisException;
use Tests\TestCase;

class QueueWorkerHeartbeatTest extends TestCase
{
    public function test_it_reports_a_heartbeat_for_each_listened_queue(): void
    {
        Redis::shouldReceive('hset')->twice()->withArgs(fn (string $key, string $worker, int $timestamp) => in_array($key, ['queue_worker_heartbeats:default', 'queue_worker_heartbeats:matomo'], true) && $worker !== '' && $timestamp === now()->timestamp);
        Redis::shouldReceive('expire')->twice()->withArgs(fn (string $key, int $ttl) => in_array($key, ['queue_worker_heartbeats:default', 'queue_worker_heartbeats:matomo'], true) && $ttl === 90);

        (new QueueWorkerHeartbeat)->report('default, matomo');
    }

    public function test_it_counts_only_fresh_worker_heartbeats(): void
    {
        Redis::shouldReceive('hgetall')->once()->with('queue_worker_heartbeats:default')->andReturn([
            'worker-one' => now()->subSeconds(10)->timestamp,
            'worker-two' => now()->subSeconds(89)->timestamp,
            'worker-three' => now()->subSeconds(91)->timestamp,
        ]);

        $this->assertSame(2, (new QueueWorkerHeartbeat)->active_workers('default'));
    }

    public function test_it_reports_unavailable_when_redis_cannot_be_reached(): void
    {
        Redis::shouldReceive('hgetall')->once()->andThrow(new RedisException('Connection refused'));

        $this->assertNull((new QueueWorkerHeartbeat)->active_workers('default'));
    }
}
