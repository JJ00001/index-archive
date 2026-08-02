<?php

namespace Database\Factories;

use App\Domain\Sec\Imports\Enums\ImportRunStatus;
use App\Domain\Sec\Imports\Models\DatasetRelease;
use App\Domain\Sec\Imports\Models\ImportRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ImportRun> */
class ImportRunFactory extends Factory
{
    /** @var class-string<ImportRun> */
    protected $model = ImportRun::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'sec_dataset_release_id' => DatasetRelease::factory(),
            'status' => ImportRunStatus::Running,
            'started_at' => now(),
            'finished_at' => null,
            'duration_ms' => null,
            'counts' => [],
            'warning_count' => 0,
            'failure_message' => null,
        ];
    }
}
