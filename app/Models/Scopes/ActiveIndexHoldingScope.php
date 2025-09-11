<?php

namespace App\Models\Scopes;

use App\Models\MarketData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActiveIndexHoldingScope implements Scope
{

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereHas('company', function (Builder $companyQ) {
            $companyQ->whereHas('marketDatas', function (Builder $marketDataQ) {
                $marketDataQ->where('date', MarketData::maxDate());
            });
        });
    }

}