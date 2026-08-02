<?php

namespace Database\Factories;

use App\Domain\Sec\Imports\Models\DataQualityIssue;
use App\Domain\Sec\Imports\Models\DatasetRelease;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DataQualityIssue> */
class DataQualityIssueFactory extends Factory
{
    /** @var class-string<DataQualityIssue> */
    protected $model = DataQualityIssue::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'sec_import_run_id' => null,
            'sec_dataset_release_id' => DatasetRelease::factory(),
            'source_file' => 'FUND_REPORTED_HOLDING.tsv',
            'source_line' => fake()->numberBetween(2, 1_000_000),
            'source_key' => fake()->uuid(),
            'reason_code' => 'invalid_source_value',
            'message' => fake()->sentence(),
            'prevents_publication' => false,
            'raw_data' => null,
        ];
    }
}
