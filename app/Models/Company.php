<?php

namespace App\Models;

use App\Models\Scopes\ActiveConstituentScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy(ActiveConstituentScope::class)]
class Company extends Model
{
    protected $fillable = [
        'name',
        'ticker',
        'isin',
        'logo',
        'sector_id',
        'country_id',
        'exchange_id',
        'currency_id',
        'asset_class_id',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function exchange(): BelongsTo
    {
        return $this->belongsTo(Exchange::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function assetClass(): BelongsTo
    {
        return $this->belongsTo(AssetClass::class);
    }

    public function marketDatas(): HasMany
    {
        return $this->hasMany(MarketData::class, 'company_id');
    }

    public function scopeWithStats(Builder $query): void
    {
        $latestDate = MarketData::max('date');

        $subQuery = MarketData::selectRaw('
            company_id,
            weight as latest_weight,
            DENSE_RANK() OVER (ORDER BY weight DESC) as `rank`,
            market_capitalization
        ')
            ->where('date', $latestDate);

        $query
            ->joinSub($subQuery, 'stats', function ($join) {
                $join->on('companies.id', '=', 'stats.company_id');
            })
            ->addSelect('companies.*', 'stats.latest_weight', 'stats.rank', 'stats.market_capitalization');
    }
}
