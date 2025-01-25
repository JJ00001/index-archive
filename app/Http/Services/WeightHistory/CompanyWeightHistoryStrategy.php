<?php

namespace App\Http\Services\WeightHistory;

use Illuminate\Support\Facades\DB;

class CompanyWeightHistoryStrategy extends BaseWeightHistoryStrategy
{
    public function fetchWeightHistory(int $id): array
    {
        return DB::select('
            SELECT date, weight
            FROM market_data
            WHERE company_id = ?
            ORDER BY date
        ', [$id]);
    }
}
