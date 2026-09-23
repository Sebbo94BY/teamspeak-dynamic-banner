<?php

namespace Tests\Feature\Support;

use App\Models\QueueMetricSnapshot;
use App\Support\QueueMetrics;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueueMetricsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-23 12:00:00');
        Config::set('queue.default', 'database');
        Config::set('queue.connections.database.queue', 'default');
        Config::set('matomo.enabled', false);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_separates_the_current_queue_states(): void
    {
        $this->insertJob(['created_at' => now()->subMinute()->timestamp]);
        $this->insertJob(['reserved_at' => now()->subSeconds(30)->timestamp]);
        $this->insertJob(['available_at' => now()->addMinute()->timestamp]);
        $this->insertJob(['reserved_at' => now()->subSeconds(100)->timestamp]);

        $metrics = (new QueueMetrics)->current('default');

        $this->assertSame([
            'total' => 4,
            'ready' => 1,
            'processing' => 1,
            'scheduled' => 1,
            'stale' => 1,
            'oldest_wait_seconds' => 60,
        ], $metrics);
    }

    public function test_it_records_queue_snapshots_and_completed_jobs(): void
    {
        $this->insertJob();
        $metrics = new QueueMetrics;

        $metrics->record();
        $metrics->record_completed_job('default', 120);
        $metrics->record_completed_job('default', 80);

        $snapshot = QueueMetricSnapshot::firstOrFail();
        $this->assertSame(1, $snapshot->total);
        $this->assertSame(2, $snapshot->processed);
        $this->assertSame(200, $snapshot->processing_time_ms);
    }

    public function test_it_ignores_completed_jobs_from_unmonitored_queues(): void
    {
        (new QueueMetrics)->record_completed_job('sync', 100);

        $this->assertDatabaseCount('queue_metric_snapshots', 0);
    }

    public function test_it_includes_the_matomo_queue_only_when_enabled(): void
    {
        $metrics = new QueueMetrics;
        $this->assertSame(['default'], $metrics->queues());

        Config::set('matomo.enabled', true);

        $this->assertSame(['default', 'matomo'], $metrics->queues());
    }

    /** @param array<string, int|null> $overrides */
    private function insertJob(array $overrides = []): void
    {
        DB::table('jobs')->insert(array_merge([
            'queue' => 'default',
            'payload' => '{}',
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => now()->timestamp,
            'created_at' => now()->timestamp,
        ], $overrides));
    }
}
