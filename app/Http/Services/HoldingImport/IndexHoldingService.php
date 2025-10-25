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
        $newHoldings = $this->buildNewHoldings($index, $companies, $companyIds);
        $existingHoldings = $this->getExistingHoldings($index);
        $filteredHoldings = $this->filterExistingHoldings($newHoldings, $existingHoldings);

        if (! empty($filteredHoldings)) {
            IndexHolding::insert($filteredHoldings);
        }
    }

    private function getCompanyIds(Collection $companies): Collection
    {
        $companyIsins = $companies->pluck('isin')->toArray();

        return Company::withoutGlobalScopes()
            ->whereIn('isin', $companyIsins)
            ->pluck('id', 'isin');
    }

    private function buildNewHoldings(
        Index $index,
        Collection $companies,
        Collection $companyIds
    ): array {
        $newHoldings = [];

        foreach ($companies as $companyData) {
            $companyId = $companyIds[$companyData->isin] ?? null;
            if ($companyId) {
                $newHoldings[] = [
                    'index_id' => $index->id,
                    'company_id' => $companyId,
                ];
            }
        }

        return $newHoldings;
    }

    private function getExistingHoldings(Index $index): array
    {
        return IndexHolding::withoutGlobalScopes()
            ->where('index_id', $index->id)
            ->pluck('company_id')
            ->toArray();
    }

    private function filterExistingHoldings(array $newHoldings, array $existingHoldings): array
    {
        return array_filter($newHoldings, function ($holding) use ($existingHoldings) {
            return ! in_array($holding['company_id'], $existingHoldings);
        });
    }
}
