<?php

namespace App\Http\Services\WeightHistory;

use Illuminate\Support\Facades\DB;

class CompanyWeightHistoryStrategy extends BaseWeightHistoryStrategy
{
    public function fetchWeightHistory(int $id, int $indexId): array
    {
        return DB::select('
            SELECT date, weight
            FROM market_data
            JOIN index_holdings ON index_holdings.id = market_data.index_holding_id
            WHERE index_holdings.company_id = ? AND index_holdings.index_id = ?
            ORDER BY date
        ', [$id, $indexId]);
    }
}
