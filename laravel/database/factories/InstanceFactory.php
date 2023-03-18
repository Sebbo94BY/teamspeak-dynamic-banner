<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Instance>
 */
class InstanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'virtualserver_name' => fake()->text(30),
            'host' => fake()->domainName(),
            'voice_port' => fake()->numberBetween(1024, 65535),
            'serverquery_port' => fake()->numberBetween(1024, 65535),
            'is_ssh' => fake()->boolean(),
            'serverquery_username' => fake()->userName(),
            'serverquery_password' => fake()->password(),
            'client_nickname' => fake()->name(),
            'default_channel_id' => fake()->randomNumber(),
            'autostart_enabled' => fake()->boolean(),
        ];
    }
}
