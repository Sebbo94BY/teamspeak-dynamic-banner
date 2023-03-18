<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Template>
 */
class TemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'alias' => fake()->text(32),
            'filename' => fake()->text(32).'.png',
            'file_path_original' => 'uploads/templates',
            'file_path_drawed_grid' => 'uploads/templates/drawed_grid',
            'file_path_drawed_grid_text' => 'uploads/templates/drawed_grid_text',
            'file_path_drawed_text' => 'uploads/templates/drawed_text',
            'width' => fake()->numberBetween(468, 1024),
            'height' => fake()->numberBetween(60, 300),
        ];
    }
}
