<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'sector_id');
    }

    public function scopeWithWeightInIndex(Builder $query, Index $index): void
    {
        $query->addSelect([
            'weight' => $index->latestMarketData()
                ->join('companies', 'companies.id', '=', 'index_holdings.company_id')
                ->whereColumn('companies.sector_id', 'sectors.id')
                ->selectRaw('SUM(market_data.weight)'),
        ]);
    }

    public function scopeWithCompaniesCountInIndex(Builder $query, Index $index): void
    {
        $query->addSelect([
            'companies_count' => $index->latestMarketData()
                                       ->join('companies', 'companies.id', '=', 'index_holdings.company_id')
                                       ->whereColumn('companies.sector_id', 'sectors.id')
                                       ->selectRaw('COUNT(DISTINCT companies.id)'),
        ]);
    }

    public function scopeWithStatsInIndex(Builder $query, Index $index): void
    {
        $query
            ->withWeightInIndex($index)
            ->withCompaniesCountInIndex($index);
    }
}
