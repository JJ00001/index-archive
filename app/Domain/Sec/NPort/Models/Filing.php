<?php

namespace App\Domain\Sec\NPort\Models;

use App\Domain\Sec\Imports\Models\DatasetRelease;
use Database\Factories\FilingFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[UseFactory(FilingFactory::class)]
class Filing extends Model
{
    use HasFactory;

    protected $table = 'nport_filings';

    protected $fillable = [
        'registrant_id',
        'first_seen_dataset_release_id',
        'accession_number',
        'filing_date',
        'file_number',
        'submission_type',
        'report_ending_period',
        'report_date',
        'is_last_filing',
        'supersedes_filing_id',
        'is_effective',
        'registrant_snapshot',
    ];

    /** @return BelongsTo<DatasetRelease, $this> */
    public function firstSeenDatasetRelease(): BelongsTo
    {
        return $this->belongsTo(DatasetRelease::class, 'first_seen_dataset_release_id');
    }

    /** @return BelongsTo<Registrant, $this> */
    public function registrant(): BelongsTo
    {
        return $this->belongsTo(Registrant::class, 'registrant_id');
    }

    /** @return BelongsTo<Filing, $this> */
    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(self::class, 'supersedes_filing_id');
    }

    /** @return HasMany<Filing, $this> */
    public function corrections(): HasMany
    {
        return $this->hasMany(self::class, 'supersedes_filing_id');
    }

    /** @return HasOne<FundReport, $this> */
    public function fundReport(): HasOne
    {
        return $this->hasOne(FundReport::class, 'filing_id');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'filing_date' => 'immutable_date',
            'report_ending_period' => 'immutable_date',
            'report_date' => 'immutable_date',
            'is_last_filing' => 'boolean',
            'is_effective' => 'boolean',
            'registrant_snapshot' => 'array',
        ];
    }
}
