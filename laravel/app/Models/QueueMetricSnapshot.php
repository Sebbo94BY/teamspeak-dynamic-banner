<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueMetricSnapshot extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'recorded_at',
        'queue',
        'total',
        'ready',
        'processing',
        'scheduled',
        'stale',
        'oldest_wait_seconds',
        'processed',
        'processing_time_ms',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
        ];
    }
}
