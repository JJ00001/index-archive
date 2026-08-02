<?php

namespace App\Domain\Sec\Imports\Models;

use App\Domain\Sec\Imports\Enums\DatasetType;
use Database\Factories\DatasetReleaseFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(DatasetReleaseFactory::class)]
class DatasetRelease extends Model
{
    use HasFactory;

    protected $table = 'sec_dataset_releases';

    protected $fillable = [
        'dataset',
        'year',
        'quarter',
        'version',
        'source_url',
        'archive_disk',
        'archive_path',
        'byte_size',
        'sha256',
        'retrieved_at',
    ];

    /** @return HasMany<ImportRun, $this> */
    public function importRuns(): HasMany
    {
        return $this->hasMany(ImportRun::class, 'sec_dataset_release_id');
    }

    /** @return HasMany<DataQualityIssue, $this> */
    public function dataQualityIssues(): HasMany
    {
        return $this->hasMany(DataQualityIssue::class, 'sec_dataset_release_id');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'dataset' => DatasetType::class,
            'retrieved_at' => 'immutable_datetime',
        ];
    }
}
