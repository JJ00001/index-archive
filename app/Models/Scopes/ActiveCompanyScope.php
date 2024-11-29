<?php

namespace App\Models\Scopes;

use App\Models\MarketData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActiveCompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereHas('marketDatas', function (Builder $builder) {
            $builder->where('date', MarketData::maxDate());
        });
    }
}
