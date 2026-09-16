<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banner_templates', function (Blueprint $table) {
            // API template selection and the five-minute state-management queries.
            $table->index(['banner_id', 'enabled'], 'banner_templates_banner_enabled_index');
            $table->index(['enable_at', 'enabled'], 'banner_templates_enable_at_enabled_index');
            $table->index(['disable_at', 'enabled'], 'banner_templates_disable_at_enabled_index');
            $table->index(['time_based_enable_at', 'enabled'], 'banner_templates_time_enable_enabled_index');
            $table->index(['time_based_disable_at', 'enabled'], 'banner_templates_time_disable_enabled_index');
            $table->index(['twitch_streamer_id', 'enabled'], 'banner_templates_twitch_enabled_index');
        });
    }

    public function down(): void
    {
        Schema::table('banner_templates', function (Blueprint $table) {
            $table->dropIndex('banner_templates_banner_enabled_index');
            $table->dropIndex('banner_templates_enable_at_enabled_index');
            $table->dropIndex('banner_templates_disable_at_enabled_index');
            $table->dropIndex('banner_templates_time_enable_enabled_index');
            $table->dropIndex('banner_templates_time_disable_enabled_index');
            $table->dropIndex('banner_templates_twitch_enabled_index');
        });
    }
};
