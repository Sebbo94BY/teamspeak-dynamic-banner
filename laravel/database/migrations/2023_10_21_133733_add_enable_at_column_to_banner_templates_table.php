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
            $table->dateTime('enable_at')->nullable()->before('disable_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner_templates', function (Blueprint $table) {
            $table->dropColumn('enable_at');
        });
    }
};
