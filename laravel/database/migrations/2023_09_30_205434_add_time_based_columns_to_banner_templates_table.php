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
        Schema::table('banner_templates', function (Blueprint $table) {
            $table->time('time_based_enable_at')->nullable()->after('disable_at');
            $table->time('time_based_disable_at')->nullable()->after('time_based_enable_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner_templates', function (Blueprint $table) {
            $table->dropColumn('time_based_disable_at');
            $table->dropColumn('time_based_enable_at');
        });
    }
};
