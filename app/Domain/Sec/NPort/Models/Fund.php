<?php

namespace App\Domain\Sec\NPort\Models;

use Database\Factories\FundFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(FundFactory::class)]
class Fund extends Model
{
    use HasFactory;

    protected $table = 'nport_funds';

    protected $fillable = [
        'registrant_id',
        'series_id',
        'name',
        'series_lei',
    ];

    /** @return BelongsTo<Registrant, $this> */
    public function registrant(): BelongsTo
    {
        return $this->belongsTo(Registrant::class, 'registrant_id');
    }

    /** @return HasMany<FundReport, $this> */
    public function fundReports(): HasMany
    {
        return $this->hasMany(FundReport::class, 'fund_id');
    }
}
