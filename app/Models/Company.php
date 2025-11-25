<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{

    use HasFactory;

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

    protected $appends = ['logo'];

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

    public function indexHoldings(): HasMany
    {
        return $this->hasMany(IndexHolding::class);
    }

    public function marketDatas(): HasManyThrough
    {
        return $this->hasManyThrough(MarketData::class, IndexHolding::class);
    }

    public function logo(): Attribute
    {
        return Attribute::make(
            get: fn(): ?string => $this->localLogoPath() ?? $this->brandfetchLogoUrl()
        );
    }

    protected function localLogoPath(): ?string
    {
        $filePath = 'logos/'.$this->isin.'.png';

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->url($filePath);
        }

        return null;
    }

    protected function brandfetchLogoUrl(): ?string
    {
        if (config('app.env') === 'local') {
            return null;
        }

        $clientId = config('app.brandfetch_api_key');

        $identifier = $this->isin;

        $encodedIdentifier = rawurlencode($identifier);
        $query             = http_build_query(['c' => $clientId]);

        return 'https://cdn.brandfetch.io/'.$encodedIdentifier.'/fallback/404?'.$query;
    }

}
