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
        Schema::create('twitch_streamers', function (Blueprint $table) {
            $table->id();
            $table->string('stream_url');
            $table->boolean('is_live')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->string('game_name')->nullable();
            $table->string('title')->nullable();
            $table->integer('viewer_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twitch_streamers');
    }
};
