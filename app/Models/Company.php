<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $appends = [
        'latest_weight',
    ];

    protected function latestWeight(): Attribute
    {
        return new Attribute(fn() => $this->marketDatas()->latest()->first()->weight);
    }

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
}
