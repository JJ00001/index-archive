<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Index extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'index_provider_id',
        'description',
        'currency',
    ];

    public function dataSource(): HasOne
    {
        return $this->hasOne(DataSource::class);
    }

    public function indexHoldings(): HasMany
    {
        return $this->hasMany(IndexHolding::class);
    }

    public function indexProvider(): BelongsTo
    {
        return $this->belongsTo(IndexProvider::class);
    }

    public function sectorStats(): Collection
    {
        return $this->latestMarketData()
                    ->with('indexHolding.company.sector')
                    ->get()
                    ->groupBy('indexHolding.company.sector.id')
                    ->map(function ($marketDataItems, $sectorId) {
                        $sector = $marketDataItems->first()->indexHolding->company->sector;

                        return (object)[
                            'id' => $sector->id,
                            'name' => $sector->name,
                            'weight' => $marketDataItems->sum('weight'),
                            'companies_count' => $marketDataItems->count(),
                        ];
                    })
                    ->sortByDesc('weight')
                    ->values();
    }

    public function latestMarketData(): HasManyThrough
    {
        return $this
            ->hasManyThrough(MarketData::class, IndexHolding::class)
            ->where('date', $this->latestMarketDataDate());
    }

    public function latestMarketDataDate(): ?string
    {
        return $this->marketData()->max('date');
    }

    public function marketData(): HasManyThrough
    {
        return $this->hasManyThrough(MarketData::class, IndexHolding::class);
    }

}
