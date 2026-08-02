<?php

namespace Database\Factories;

use App\Domain\Sec\Imports\Enums\DatasetType;
use App\Domain\Sec\Imports\Models\DatasetRelease;
use App\Domain\Sec\NPort\Data\NPortQuarter;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DatasetRelease> */
class DatasetReleaseFactory extends Factory
{
    /** @var class-string<DatasetRelease> */
    protected $model = DatasetRelease::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $quarter = NPortQuarter::firstPublic();
        $sha256 = fake()->unique()->sha256();

        return [
            'dataset' => DatasetType::NPort,
            'year' => $quarter->year,
            'quarter' => $quarter->quarter,
            'version' => fake()->unique()->numberBetween(1, 2_147_483_647),
            'source_url' => "https://www.sec.gov/files/dera/data/form-n-port-data-sets/{$quarter->slug()}_nport.zip",
            'archive_disk' => 'local',
            'archive_path' => "sec/nport/{$quarter->year}/q{$quarter->quarter}/{$sha256}.zip",
            'byte_size' => fake()->numberBetween(100_000_000, 600_000_000),
            'sha256' => $sha256,
            'retrieved_at' => now(),
        ];
    }
}
