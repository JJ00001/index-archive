<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IndexProvider>
 */
class IndexProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'shorthand' => fake()->regexify('[A-Z]{2,4}'),
            'description' => fake()->paragraph(),
            'website' => fake()->url(),
        ];
    }
}
