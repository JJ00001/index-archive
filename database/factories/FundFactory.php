<?php

namespace Database\Factories;

use App\Domain\Sec\NPort\Models\Fund;
use App\Domain\Sec\NPort\Models\Registrant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Fund> */
class FundFactory extends Factory
{
    /** @var class-string<Fund> */
    protected $model = Fund::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'registrant_id' => Registrant::factory(),
            'series_id' => 'S'.fake()->unique()->numerify('#########'),
            'name' => fake()->company().' Portfolio',
            'series_lei' => strtoupper(fake()->bothify('??????????????????##')),
        ];
    }
}
