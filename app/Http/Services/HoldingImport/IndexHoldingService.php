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

        $companyIds = $this->getCompanyIds($companies);
        $existingHoldings = $this->getExistingHoldings($index);
        $newHoldings = $this->buildNewHoldings($index, $companies, $companyIds, $existingHoldings);

        if (! empty($newHoldings)) {
            IndexHolding::insert($newHoldings);
        }
    }

    private function getCompanyIds(Collection $companies): Collection
    {
        $companyIsins = $companies->pluck('isin')->toArray();

        return Company::withoutGlobalScopes()
            ->whereIn('isin', $companyIsins)
            ->pluck('id', 'isin');
    }

    private function getExistingHoldings(Index $index): array
    {
        return IndexHolding::withoutGlobalScopes()
            ->where('index_id', $index->id)
            ->pluck('company_id')
            ->toArray();
    }

    private function buildNewHoldings(
        Index $index,
        Collection $companies,
        Collection $companyIds,
        array $existingHoldings
    ): array {
        $newHoldings = [];

        foreach ($companies as $companyData) {
            $companyId = $companyIds[$companyData->isin] ?? null;
            if ($companyId && ! in_array($companyId, $existingHoldings)) {
                $newHoldings[] = [
                    'index_id' => $index->id,
                    'company_id' => $companyId,
                ];
            }
        }

        return $newHoldings;
    }
}
