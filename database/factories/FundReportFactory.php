<?php

namespace Database\Factories;

use App\Domain\Sec\NPort\Models\Filing;
use App\Domain\Sec\NPort\Models\Fund;
use App\Domain\Sec\NPort\Models\FundReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FundReport> */
class FundReportFactory extends Factory
{
    /** @var class-string<FundReport> */
    protected $model = FundReport::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'filing_id' => Filing::factory(),
            'fund_id' => Fund::factory(),
            'series_name' => fake()->company().' Portfolio',
            'reported_series_id' => 'S'.fake()->numerify('#########'),
            'series_lei' => strtoupper(fake()->bothify('??????????????????##')),
            'total_assets' => '1000000.000000000000',
            'total_liabilities' => '100000.000000000000',
            'net_assets' => '900000.000000000000',
            'is_non_cash_collateral' => false,
        ];
    }

    public function withoutFund(): static
    {
        return $this->state(fn (): array => [
            'fund_id' => null,
            'reported_series_id' => null,
        ]);
    }
}
