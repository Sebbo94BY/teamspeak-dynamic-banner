<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instances', function (Blueprint $table): void {
            $table->unsignedTinyInteger('bot_recovery_attempts')->default(0)->after('health_problem_started_at');
            $table->timestamp('bot_restart_scheduled_at')->nullable()->after('bot_recovery_attempts');
            $table->text('bot_restart_reason')->nullable()->after('bot_restart_scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('instances', function (Blueprint $table): void {
            $table->dropColumn(['bot_recovery_attempts', 'bot_restart_scheduled_at', 'bot_restart_reason']);
        });
    }
};
