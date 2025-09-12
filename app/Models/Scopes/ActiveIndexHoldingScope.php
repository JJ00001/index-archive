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
        // TODO Refactor for readability

        $sql = "
            SELECT ih.id
            FROM index_holdings ih
            INNER JOIN market_data md ON md.index_holding_id = ih.id
            INNER JOIN (
                SELECT ih2.index_id, MAX(md2.date) as max_date
                FROM index_holdings ih2
                INNER JOIN market_data md2 ON md2.index_holding_id = ih2.id
                GROUP BY ih2.index_id
            ) max_dates ON max_dates.index_id = ih.index_id AND md.date = max_dates.max_date
        ";

        $builder->whereIn('index_holdings.id', function ($query) use ($sql) {
            $query->selectRaw('id')->fromRaw("($sql) as active_holdings");
        });
    }

}