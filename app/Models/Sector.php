<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    protected $fillable = [
        'name',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'sector_id');
    }

    public function scopeWithCompaniesCount(Builder $query): void
    {
        $query->withCount('companies');
    }

    public function scopeWithWeight(Builder $query): void
    {
        $latestDate = MarketData::max('date');

        $query->addSelect([
            'weight' => function ($query) use ($latestDate) {
                $query->selectRaw('SUM(market_data.weight)')
                    ->from('companies')
                    ->join('market_data', function ($join) use ($latestDate) {
                        $join->on('companies.id', '=', 'market_data.company_id')
                            ->where('market_data.date', $latestDate);
                    })
                    ->whereColumn('companies.sector_id', 'sectors.id');
            },
        ]);
    }

    public function scopeWithStats(Builder $query): void
    {
        $query
            ->withCompaniesCount()
            ->withWeight();
    }
}
