<?php

use App\Models\Localization;
use App\Models\User;
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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('localization_id')->default(1)->after('password');
        });

        // Set the first available language as default for every existing user.
        $first_localization = Localization::first();

        foreach (User::all() as $user) {
            $user->localization_id = $first_localization->id;
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('localization_id');
        });
    }
};
