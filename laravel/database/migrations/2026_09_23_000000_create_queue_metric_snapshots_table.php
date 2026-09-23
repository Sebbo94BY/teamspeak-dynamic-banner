<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('queue_metric_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('queue');
            $table->timestamp('recorded_at');
            $table->unsignedInteger('total');
            $table->unsignedInteger('ready');
            $table->unsignedInteger('processing');
            $table->unsignedInteger('scheduled');
            $table->unsignedInteger('stale');
            $table->unsignedInteger('oldest_wait_seconds');
            $table->unique(['queue', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_metric_snapshots');
    }
};
