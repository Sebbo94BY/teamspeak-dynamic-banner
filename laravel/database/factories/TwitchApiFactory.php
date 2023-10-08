<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\User>
 */
class TwitchApiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'client_id' => Str::random(32),
            'client_secret' => Str::random(32),
            'access_token' => Str::random(32),
            'expires_at' => fake()->dateTime(Carbon::now()->addDays(30)),
        ];
    }
}
