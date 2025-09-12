<?php

namespace App\Models\Scopes;

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
        $builder->whereHas('marketData', function (Builder $marketDataQ) {
            $marketDataQ->whereRaw(
                '
                date = (
                    SELECT MAX(md.date) 
                    FROM market_data md 
                    JOIN index_holdings ih ON md.index_holding_id = ih.id 
                    WHERE ih.index_id = (
                        SELECT index_id 
                        FROM index_holdings 
                        WHERE id = market_data.index_holding_id
                    )
                )
            '
            );
        });
    }

}