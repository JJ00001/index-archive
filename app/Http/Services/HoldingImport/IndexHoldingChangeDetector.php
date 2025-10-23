<?php

namespace App\Http\Services\HoldingImport;

use App\Models\Company;
use App\Models\Index;
use App\Models\IndexHolding;
use Illuminate\Support\Collection;

class IndexHoldingChangeDetector
{
    public function detectChanges(Index $index, Collection $updatedCompanies): array
    {
        $updatedCompanyIsins = $updatedCompanies->pluck('isin');

        $updatedCompanyIds = Company::whereIn('isin', $updatedCompanyIsins)
            ->pluck('id');

        $currentCompanyIds = IndexHolding::where('index_id', $index->id)
            ->pluck('company_id');

        $addedCompanyIds = $updatedCompanyIds->diff($currentCompanyIds);
        $removedCompanyIds = $currentCompanyIds->diff($updatedCompanyIds);

        return [
            'additions' => Company::whereIn('id', $addedCompanyIds)->get(),
            'removals' => Company::whereIn('id', $removedCompanyIds)->get(),
        ];
    }
}
