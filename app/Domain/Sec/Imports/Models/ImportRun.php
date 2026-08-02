<?php

namespace App\Domain\Sec\Imports\Models;

use App\Domain\Sec\Imports\Enums\ImportRunStatus;
use Database\Factories\ImportRunFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Throwable;

#[UseFactory(ImportRunFactory::class)]
class ImportRun extends Model
{
    use HasFactory;

    protected $table = 'sec_import_runs';

    protected $fillable = [
        'sec_dataset_release_id',
        'status',
        'started_at',
        'finished_at',
        'duration_ms',
        'counts',
        'warning_count',
        'failure_message',
    ];

    /** @return BelongsTo<DatasetRelease, $this> */
    public function datasetRelease(): BelongsTo
    {
        return $this->belongsTo(DatasetRelease::class, 'sec_dataset_release_id');
    }

    /** @return HasMany<DataQualityIssue, $this> */
    public function dataQualityIssues(): HasMany
    {
        return $this->hasMany(DataQualityIssue::class, 'sec_import_run_id');
    }

    /** @param array<string, mixed> $counts */
    public function complete(array $counts, int $durationMs): void
    {
        $this->update([
            'status' => ImportRunStatus::Completed,
            'counts' => $counts,
            'duration_ms' => $durationMs,
            'warning_count' => 0,
            'failure_message' => null,
            'finished_at' => now(),
        ]);
    }

    /** @param array<string, mixed> $counts */
    public function completeWithWarnings(array $counts, int $durationMs, int $warningCount): void
    {
        $this->update([
            'status' => ImportRunStatus::CompletedWithWarnings,
            'counts' => $counts,
            'duration_ms' => $durationMs,
            'warning_count' => $warningCount,
            'failure_message' => null,
            'finished_at' => now(),
        ]);
    }

    public function fail(Throwable $exception): void
    {
        $this->update([
            'status' => ImportRunStatus::Failed,
            'failure_message' => $exception->getMessage(),
            'finished_at' => now(),
        ]);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => ImportRunStatus::class,
            'started_at' => 'immutable_datetime',
            'finished_at' => 'immutable_datetime',
            'counts' => 'array',
        ];
    }
}
