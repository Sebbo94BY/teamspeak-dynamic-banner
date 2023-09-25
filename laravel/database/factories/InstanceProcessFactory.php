<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\InstanceProcess>
 */
class InstanceProcessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'command' => 'php artisan instance:start-teamspeak-bot '.fake()->randomNumber(),
            'process_id' => fake()->randomNumber(),
        ];
    }
}
