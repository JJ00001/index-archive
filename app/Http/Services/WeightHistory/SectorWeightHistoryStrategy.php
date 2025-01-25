<?php

namespace App\Http\Services\WeightHistory;

use Illuminate\Support\Facades\DB;

class SectorWeightHistoryStrategy extends BaseWeightHistoryStrategy
{
    public function fetchWeightHistory(int $id): array
    {
        $query = "
            SELECT
              market_data.date,
              SUM(market_data.weight) AS weight
            FROM
              market_data
              JOIN companies ON companies.id = market_data.company_id
            WHERE
              companies.sector_id = ?
            GROUP BY
              market_data.date
            ORDER BY
              market_data.date
        ";

        return DB::select($query, [$id]);
    }
}
