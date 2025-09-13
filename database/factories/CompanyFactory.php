<?php

namespace Database\Factories;

use App\Models\AssetClass;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
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
            'ticker' => fake()->regexify('[A-Z]{2,5}'),
            'isin' => fake()->regexify('[A-Z]{2}[0-9]{10}'),
            'sector_id' => Sector::factory(),
            'country_id' => Country::factory(),
            'exchange_id' => Exchange::factory(),
            'currency_id' => Currency::factory(),
            'asset_class_id' => AssetClass::factory(),
        ];
    }

}
