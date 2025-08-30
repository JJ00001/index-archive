<?php

namespace App\Models\Scopes;

use App\Models\MarketData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyStatsScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $subQuery = MarketData::selectRaw('
            index_holdings.company_id,
            weight as latest_weight,
            DENSE_RANK() OVER (ORDER BY weight DESC) as `rank`,
            market_capitalization
        ')
                              ->join('index_holdings', 'index_holdings.id', '=', 'market_data.index_holding_id')
            ->where('date', MarketData::maxDate());

        $builder
            ->joinSub($subQuery, 'stats', function ($join) {
                $join->on('companies.id', '=', 'stats.company_id');
            })
            ->addSelect('companies.*', 'stats.latest_weight', 'stats.rank', 'stats.market_capitalization');

    }
}
