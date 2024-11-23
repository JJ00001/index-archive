<?php

namespace App\Models;

use App\Models\Scopes\ActiveConstituentScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Number;

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

    protected $appends = ['market_cap'];

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

    public function scopeWithLatestWeight(Builder $query): void
    {
        $query->addSelect([
            'latest_weight' => MarketData::select('weight')
                ->whereColumn('company_id', 'companies.id')
                ->latest()
                ->limit(1),
        ]);
    }

    protected function marketCap(): Attribute
    {
        $marketCap = $this->marketDatas()->latest()->first()->market_capitalization;
        $marketCapFormatted = Number::forHumans($marketCap, 2, abbreviate: true);

        return new Attribute(
            get: fn() => $marketCapFormatted,
        );
    }
}
