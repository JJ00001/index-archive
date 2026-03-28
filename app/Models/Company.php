<?php

namespace App\Models;

use App\Support\Seo\CompanySeoData;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class Company extends Model implements Sitemapable
{
    use HasFactory;
    use HasSEO;

    protected $fillable = [
        'name',
        'ticker',
        'isin',
        'has_stored_logo',
        'sector_id',
        'country_id',
        'exchange_id',
        'currency_id',
        'asset_class_id',
    ];

    protected $casts = [
        'has_stored_logo' => 'bool',
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
            get: fn(): ?string => $this->storedLogoUrl() ?? $this->brandfetchLogoUrl()
        );
    }

    protected function storedLogoUrl(): ?string
    {
        if ( ! $this->has_stored_logo) {
            return null;
        }

        return Storage::disk()->url('logos/'.$this->isin.'.png');
    }

    protected function brandfetchLogoUrl(): ?string
    {
        if (config('app.env') === 'local') {
            return null;
        }

        $clientId = config('app.brandfetch_api_key');
        $encodedIdentifier = rawurlencode($this->isin);
        $query = http_build_query(['c' => $clientId]);

        return 'https://cdn.brandfetch.io/'.$encodedIdentifier.'/fallback/404?'.$query;
    }

    // SEO

    public function getDynamicSEOData(): SEOData
    {
        return CompanySeoData::for($this);
    }

    public function toSitemapTag(): Url|string|array
    {
        return CompanySeoData::sitemapTagFor($this);
    }
}
