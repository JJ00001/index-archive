<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketData>
 */
class MarketDataFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'share_price' => fake()->randomFloat(2, 1, 1000),
            'weight' => fake()->randomFloat(2, 0, 1),
            'market_capitalization' => fake()->randomFloat(2, 1, 1000000000),
        ];
    }

}
