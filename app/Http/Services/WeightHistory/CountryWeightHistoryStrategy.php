<?php

namespace App\Http\Services\WeightHistory;

use Illuminate\Support\Facades\DB;

class CountryWeightHistoryStrategy extends BaseWeightHistoryStrategy
{
    public function fetchWeightHistory(int $id): array
    {
        $query = "
            SELECT
              market_data.date,
              SUM(market_data.weight) AS weight
            FROM
              market_data
              JOIN index_holdings ON index_holdings.id = market_data.index_holding_id
              JOIN companies ON companies.id = index_holdings.company_id
            WHERE
              companies.country_id = ?
            GROUP BY
              market_data.date
            ORDER BY
              market_data.date
        ";

        return DB::select($query, [$id]);
    }
}
