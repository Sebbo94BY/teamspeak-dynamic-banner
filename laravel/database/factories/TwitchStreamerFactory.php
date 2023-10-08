<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\User>
 */
class TwitchStreamerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'stream_url' => 'https://www.twitch.tv/'.Str::random(32),
            'is_live' => fake()->boolean(),
            'started_at' => fake()->dateTime(Carbon::now()->subMinutes(30)),
            'game_name' => fake()->name(),
            'title' => fake()->text(192),
            'viewer_count' => fake()->numberBetween(),
        ];
    }
}
