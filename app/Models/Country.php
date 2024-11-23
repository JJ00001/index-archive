<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'country_id');
    }

    public function scopeWithStats(Builder $query): void
    {
        $latestDate = MarketData::max('date');

        $query->withCount('companies')
            ->addSelect([
                'weight' => function ($query) use ($latestDate) {
                    $query->selectRaw('SUM(market_data.weight)')
                        ->from('companies')
                        ->join('market_data', function ($join) use ($latestDate) {
                            $join->on('companies.id', '=', 'market_data.company_id')
                                ->where('market_data.date', $latestDate);
                        })
                        ->whereColumn('companies.country_id', 'countries.id');
                },
            ]);
    }
}
