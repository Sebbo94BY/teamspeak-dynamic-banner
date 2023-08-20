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
            $table->string('file_path_drawed_grid_text')->nullable()->comment('The uploaded file by the user, but with a grid system and the configured texts on it.')->after('template_id');
            $table->string('file_path_drawed_text')->nullable()->comment('The uploaded file by the user, but with the configured texts on it.')->after('file_path_drawed_grid_text');
        });

        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['file_path_drawed_text', 'file_path_drawed_grid_text']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('file_path_drawed_grid_text')->comment('The uploaded file by the user, but with a grid system and the configured texts on it.')->after('file_path_drawed_grid');
            $table->string('file_path_drawed_text')->comment('The uploaded file by the user, but with the configured texts on it.')->after('file_path_drawed_grid_text');
        });

        Schema::table('banner_templates', function (Blueprint $table) {
            $table->dropColumn(['file_path_drawed_text', 'file_path_drawed_grid_text']);
        });
    }
};
