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
        Schema::table('banner_configurations', function (Blueprint $table) {
            $table->string('internal_field_label')->nullable()->after('banner_template_id');
            $table->string('text_alignment')->default('left')->after('x_coordinate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner_configurations', function (Blueprint $table) {
            $table->dropColumn(['internal_field_label', 'text_alignment']);
        });
    }
};
