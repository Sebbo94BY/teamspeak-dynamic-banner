<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\BannerTemplate>
 */
class BannerTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'redirect_url' => fake()->url(),
            'file_path_drawed_grid_text' => 'uploads/templates/drawed_grid_text',
            'file_path_drawed_text' => 'uploads/templates/drawed_text',
            'enabled' => fake()->boolean(),
        ];
    }
}
