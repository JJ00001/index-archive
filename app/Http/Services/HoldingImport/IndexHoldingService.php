<?php

namespace App\Http\Services\HoldingImport;

use App\Models\Company;
use App\Models\Index;
use App\Models\IndexHolding;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class IndexHoldingService
{
    public function createForIndex(Index $index, Collection $companies): void
    {
        Log::info('Creating Index Holdings...');

        $companyIsins = $companies->pluck('isin')->toArray();

        $companyIds = Company::withoutGlobalScopes()
            ->whereIn('isin', $companyIsins)
            ->pluck('id', 'isin');

        $existingHoldings = IndexHolding::withoutGlobalScopes()
            ->where('index_id', $index->id)
            ->pluck('company_id')
            ->toArray();

        $newIndexHoldings = [];
        foreach ($companies as $companyData) {
            $companyId = $companyIds[$companyData->isin] ?? null;
            if ($companyId && ! in_array($companyId, $existingHoldings)) {
                $newIndexHoldings[] = [
                    'index_id' => $index->id,
                    'company_id' => $companyId,
                ];
            }
        }

        if (! empty($newIndexHoldings)) {
            IndexHolding::insert($newIndexHoldings);
        }
    }
}
