<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instances', function (Blueprint $table): void {
            $table->boolean('health_last_check_healthy')->nullable()->after('health_last_success_at');
            $table->json('health_last_check_results')->nullable()->after('health_last_check_healthy');
            $table->json('health_channel_list')->nullable()->after('health_last_check_results');
            $table->timestamp('health_last_checked_at')->nullable()->after('health_channel_list');
            $table->timestamp('health_problem_started_at')->nullable()->after('health_last_checked_at');
        });
    }

    public function down(): void
    {
        Schema::table('instances', function (Blueprint $table): void {
            $table->dropColumn(['health_last_check_healthy', 'health_last_check_results', 'health_channel_list', 'health_last_checked_at', 'health_problem_started_at']);
        });
    }
};
