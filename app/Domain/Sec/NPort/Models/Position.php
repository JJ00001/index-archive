<?php

namespace App\Domain\Sec\NPort\Models;

use App\Domain\Securities\Models\Security;
use Database\Factories\PositionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(PositionFactory::class)]
class Position extends Model
{
    use HasFactory;

    protected $table = 'nport_positions';

    protected $fillable = [
        'fund_report_id',
        'security_id',
        'sec_holding_id',
        'issuer_name',
        'issuer_lei',
        'issuer_title',
        'issuer_cusip',
        'balance',
        'unit',
        'other_unit_description',
        'currency_code',
        'currency_value',
        'exchange_rate',
        'percentage',
        'payoff_profile',
        'asset_category',
        'other_asset_description',
        'issuer_type',
        'other_issuer_description',
        'investment_country',
        'is_restricted_security',
        'fair_value_level',
        'derivative_category',
    ];

    /** @return BelongsTo<FundReport, $this> */
    public function fundReport(): BelongsTo
    {
        return $this->belongsTo(FundReport::class, 'fund_report_id');
    }

    /** @return BelongsTo<Security, $this> */
    public function security(): BelongsTo
    {
        return $this->belongsTo(Security::class, 'security_id');
    }

    /** @return HasMany<PositionIdentifier, $this> */
    public function identifiers(): HasMany
    {
        return $this->hasMany(PositionIdentifier::class, 'position_id');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_restricted_security' => 'boolean',
        ];
    }
}
