<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Template>
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
        $random_name = str_replace('.', '', fake()->text(32));

        return [
            'alias' => $random_name,
            'filename' => str_replace(' ', '_', $random_name).'.png',
            'file_path_original' => 'uploads/templates',
            'file_path_drawed_grid' => 'uploads/templates/drawed_grid',
            'width' => fake()->numberBetween(468, 1024),
            'height' => fake()->numberBetween(60, 300),
        ];
    }
}
