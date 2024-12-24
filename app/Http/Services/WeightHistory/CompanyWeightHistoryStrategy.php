<?php

namespace App\Http\Services\WeightHistory;

use Illuminate\Support\Facades\DB;

class CompanyWeightHistoryStrategy implements WeightHistoryStrategy
{
    public function getWeightHistory(int $id): array
    {
        $companyMarketData = $this->fetchWeightHistory($id);

        return [
            'dates' => array_map(fn($data) => $data->date, $companyMarketData),
            'weights' => array_map(fn($data) => $data->weight, $companyMarketData),
        ];
    }

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
