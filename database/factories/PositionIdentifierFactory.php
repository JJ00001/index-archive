<?php

namespace Database\Factories;

use App\Domain\Sec\NPort\Models\Position;
use App\Domain\Sec\NPort\Models\PositionIdentifier;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PositionIdentifier> */
class PositionIdentifierFactory extends Factory
{
    /** @var class-string<PositionIdentifier> */
    protected $model = PositionIdentifier::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'position_id' => Position::factory(),
            'sec_identifiers_id' => fake()->unique()->numerify(str_repeat('#', 38)),
            'identifier_isin' => strtoupper(fake()->bothify('??##########')),
            'identifier_ticker' => null,
            'other_identifier' => null,
            'other_identifier_description' => null,
        ];
    }
}
