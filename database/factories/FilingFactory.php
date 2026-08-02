<?php

namespace Database\Factories;

use App\Domain\Sec\Imports\Models\DatasetRelease;
use App\Domain\Sec\NPort\Models\Filing;
use App\Domain\Sec\NPort\Models\Registrant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Filing> */
class FilingFactory extends Factory
{
    /** @var class-string<Filing> */
    protected $model = Filing::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $reportDate = fake()->dateTimeBetween('-3 months', 'now');

        return [
            'registrant_id' => Registrant::factory(),
            'first_seen_dataset_release_id' => DatasetRelease::factory(),
            'accession_number' => fake()->unique()->numerify('##########-##-######'),
            'filing_date' => fake()->dateTimeBetween($reportDate, 'now'),
            'file_number' => fake()->numerify('811-#####'),
            'submission_type' => 'NPORT-P',
            'report_ending_period' => $reportDate,
            'report_date' => $reportDate,
            'is_last_filing' => false,
            'supersedes_filing_id' => null,
            'is_effective' => true,
            'registrant_snapshot' => [
                'cik' => fake()->numerify('##########'),
                'name' => fake()->company(),
            ],
        ];
    }
}
