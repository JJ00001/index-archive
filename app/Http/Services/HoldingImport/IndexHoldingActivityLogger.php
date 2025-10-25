<?php

namespace App\Http\Services\HoldingImport;

use App\Models\Index;
use App\Models\IndexHolding;
use Illuminate\Support\Collection;

class IndexHoldingActivityLogger
{
    public function logAdditions(Index $index, Collection $companies, string $date): void
    {
        $this->logActivity($index, $companies, $date, 'company_added_to_index');
    }

    private function logActivity(Index $index, Collection $companies, string $date, string $eventType): void
    {
        $indexHolding = new IndexHolding;

        foreach ($companies as $company) {
            activity()
                ->performedOn($indexHolding)
                ->withProperties([
                    'company_id' => $company->id,
                    'index_id' => $index->id,
                    'date' => $date,
                ])
                ->log($eventType);
        }
    }

    public function logRemovals(Index $index, Collection $companies, string $date): void
    {
        $this->logActivity($index, $companies, $date, 'company_removed_from_index');
    }
}
