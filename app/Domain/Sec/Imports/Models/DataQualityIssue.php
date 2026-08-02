<?php

namespace App\Domain\Sec\Imports\Models;

use Database\Factories\DataQualityIssueFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(DataQualityIssueFactory::class)]
class DataQualityIssue extends Model
{
    use HasFactory;

    protected $table = 'sec_data_quality_issues';

    protected $fillable = [
        'sec_import_run_id',
        'sec_dataset_release_id',
        'source_file',
        'source_line',
        'source_key',
        'reason_code',
        'message',
        'prevents_publication',
        'raw_data',
    ];

    /** @return BelongsTo<ImportRun, $this> */
    public function importRun(): BelongsTo
    {
        return $this->belongsTo(ImportRun::class, 'sec_import_run_id');
    }

    /** @return BelongsTo<DatasetRelease, $this> */
    public function datasetRelease(): BelongsTo
    {
        return $this->belongsTo(DatasetRelease::class, 'sec_dataset_release_id');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'prevents_publication' => 'boolean',
            'raw_data' => 'array',
        ];
    }
}
