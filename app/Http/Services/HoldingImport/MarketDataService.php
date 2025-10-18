<?php

namespace App\Http\Services\HoldingImport;

use App\Models\Index;
use App\Models\IndexHolding;
use App\Models\MarketData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class MarketDataService
{
    public function insert(Index $index, Collection $marketData): void
    {
        Log::info('Inserting Market Data...');

        $indexHoldingIds = IndexHolding::withoutGlobalScopes()
            ->where('index_id', $index->id)
            ->join('companies', 'companies.id', '=', 'index_holdings.company_id')
            ->whereIn('companies.isin', $marketData->pluck('companyIsin')->toArray())
            ->pluck('index_holdings.id', 'companies.isin');

        $dataToInsert = [];
        foreach ($marketData as $point) {
            if (isset($indexHoldingIds[$point->companyIsin])) {
                $data = $point->toArray();
                $data['index_holding_id'] = $indexHoldingIds[$point->companyIsin];
                unset($data['company_isin']); // Remove temporary reference
                $dataToInsert[] = $data;
            } else {
                Log::warning('No matching index holding found for ISIN: '.$point->companyIsin);
            }
        }

        if (! empty($dataToInsert)) {
            MarketData::insert($dataToInsert);
        }

        Log::info('Data processing complete!');
    }
}
