<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instances', function (Blueprint $table): void {
            $table->text('health_last_error')->nullable()->after('autostart_enabled');
            $table->timestamp('health_last_error_at')->nullable()->after('health_last_error');
            $table->timestamp('health_last_success_at')->nullable()->after('health_last_error_at');
        });
    }

    public function down(): void
    {
        Schema::table('instances', function (Blueprint $table): void {
            $table->dropColumn(['health_last_error', 'health_last_error_at', 'health_last_success_at']);
        });
    }
};
