<?php

use App\Models\BannerConfiguration;
use App\Models\Font;
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
            $table->foreignId('font_id')->after('text');
        });

        foreach (BannerConfiguration::all() as $banner_configuration) {
            $banner_configuration->font_id = Font::where('filename', '=', basename($banner_configuration->fontfile_path))->first()->id;
            $banner_configuration->save();
        }

        Schema::table('banner_configurations', function (Blueprint $table) {
            $table->dropColumn('fontfile_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner_configurations', function (Blueprint $table) {
            $table->string('fontfile_path');
        });

        foreach (BannerConfiguration::all() as $banner_configuration) {
            $banner_configuration->fontfile_path = 'fonts/'.Font::find($banner_configuration->font_id)->filename;
            $banner_configuration->save();
        }

        Schema::table('banner_configurations', function (Blueprint $table) {
            $table->dropColumn('font_id');
        });
    }
};
