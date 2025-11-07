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
        $companyIdsInFile = $this->collectCompanyIdsInFile($companies, $companyIds);
        $existingHoldings = $this->getExistingHoldings($index);

        $this->deactivateMissingHoldings($existingHoldings, $companyIdsInFile);
        $this->reactivateReturningHoldings($existingHoldings, $companyIdsInFile);

        $newHoldings = $this->buildNewHoldings($index, $companies, $companyIds);
        $existingCompanyIds = $existingHoldings->pluck('company_id')->all();
        $filteredHoldings = $this->filterExistingHoldings($newHoldings, $existingCompanyIds);

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
                    'is_active' => true,
                ];
            }
        }

        return $newHoldings;
    }

    private function collectCompanyIdsInFile(Collection $companies, Collection $companyIds): array
    {
        $companyIdsInFile = [];

        foreach ($companies as $companyData) {
            $companyId = $companyIds[$companyData->isin] ?? null;
            if ($companyId) {
                $companyIdsInFile[] = $companyId;
            }
        }

        return array_values(array_unique($companyIdsInFile));
    }

    private function getExistingHoldings(Index $index): Collection
    {
        return IndexHolding::withoutGlobalScopes()
            ->where('index_id', $index->id)
            ->get(['id', 'company_id', 'is_active']);
    }

    private function deactivateMissingHoldings(Collection $existingHoldings, array $companyIdsInFile): void
    {
        $idsToDeactivate = $existingHoldings
            ->filter(fn(IndexHolding $holding) => $holding->is_active && ! in_array($holding->company_id,
                    $companyIdsInFile, true))
            ->pluck('id')
            ->all();

        if ( ! empty($idsToDeactivate)) {
            IndexHolding::withoutGlobalScopes()
                        ->whereIn('id', $idsToDeactivate)
                        ->update(['is_active' => false]);
        }
    }

    private function reactivateReturningHoldings(Collection $existingHoldings, array $companyIdsInFile): void
    {
        if (empty($companyIdsInFile)) {
            return;
        }

        $idsToActivate = $existingHoldings
            ->filter(fn(IndexHolding $holding) => ! $holding->is_active && in_array($holding->company_id,
                    $companyIdsInFile, true))
            ->pluck('id')
            ->all();

        if ( ! empty($idsToActivate)) {
            IndexHolding::withoutGlobalScopes()
                        ->whereIn('id', $idsToActivate)
                        ->update(['is_active' => true]);
        }
    }

    private function filterExistingHoldings(array $newHoldings, array $existingHoldings): array
    {
        return array_filter($newHoldings, function ($holding) use ($existingHoldings) {
            return ! in_array($holding['company_id'], $existingHoldings);
        });
    }
}
