<?php

namespace Database\Factories;

use App\Domain\Sec\NPort\Models\FundReport;
use App\Domain\Sec\NPort\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Position> */
class PositionFactory extends Factory
{
    /** @var class-string<Position> */
    protected $model = Position::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'fund_report_id' => FundReport::factory(),
            'security_id' => null,
            'sec_holding_id' => fake()->unique()->numerify(str_repeat('#', 38)),
            'issuer_name' => fake()->company(),
            'issuer_lei' => strtoupper(fake()->bothify('??????????????????##')),
            'issuer_title' => fake()->words(3, true),
            'issuer_cusip' => strtoupper(fake()->bothify('########?')),
            'balance' => '100.000000000000',
            'unit' => 'NS',
            'other_unit_description' => null,
            'currency_code' => 'USD',
            'currency_value' => '1000.000000000000',
            'exchange_rate' => '1.000000000000',
            'percentage' => '1.000000000000',
            'payoff_profile' => 'Long',
            'asset_category' => 'EC',
            'other_asset_description' => null,
            'issuer_type' => 'CORP',
            'other_issuer_description' => null,
            'investment_country' => 'US',
            'is_restricted_security' => false,
            'fair_value_level' => '1',
            'derivative_category' => null,
        ];
    }

    public function sparseSwap(): static
    {
        return $this->state(fn (): array => [
            'security_id' => null,
            'issuer_name' => null,
            'issuer_lei' => null,
            'issuer_title' => null,
            'issuer_cusip' => null,
            'balance' => null,
            'unit' => null,
            'other_unit_description' => null,
            'currency_code' => null,
            'currency_value' => null,
            'exchange_rate' => null,
            'percentage' => null,
            'payoff_profile' => null,
            'asset_category' => null,
            'other_asset_description' => null,
            'issuer_type' => null,
            'other_issuer_description' => null,
            'investment_country' => null,
            'is_restricted_security' => null,
            'fair_value_level' => null,
            'derivative_category' => 'SWP',
        ]);
    }
}
