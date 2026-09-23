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
        if (Schema::hasColumn('queue_metric_snapshots', 'queue')) {
            return;
        }

        Schema::table('queue_metric_snapshots', function (Blueprint $table) {
            $table->dropUnique(['recorded_at']);
            $table->string('queue')->default('default')->after('id');
            $table->unique(['queue', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('queue_metric_snapshots', 'queue')) {
            return;
        }

        Schema::table('queue_metric_snapshots', function (Blueprint $table) {
            $table->dropUnique(['queue', 'recorded_at']);
            $table->dropColumn('queue');
            $table->unique('recorded_at');
        });
    }
};
