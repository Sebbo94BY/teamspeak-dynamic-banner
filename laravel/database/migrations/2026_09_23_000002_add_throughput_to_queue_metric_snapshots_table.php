<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queue_metric_snapshots', function (Blueprint $table) {
            $table->unsignedInteger('processed')->default(0);
            $table->unsignedBigInteger('processing_time_ms')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('queue_metric_snapshots', function (Blueprint $table) {
            $table->dropColumn(['processed', 'processing_time_ms']);
        });
    }
};
