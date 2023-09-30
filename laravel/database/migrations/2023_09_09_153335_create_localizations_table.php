<?php

use Database\Seeders\LocalizationSeeder;
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
        Schema::create('localizations', function (Blueprint $table) {
            $table->id();
            $table->string('language_name')->unique();
            $table->string('locale');
            $table->timestamps();
        });

        // Run the localization seeder to fill the new table
        $localization_seeder = new LocalizationSeeder();
        $localization_seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localizations');
    }
};
