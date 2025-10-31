<?php

namespace App\Http\Resources;

use App\Models\IndexHolding;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexHoldingCompanyCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $rankedCompanies = $this->collection
            ->map(fn (IndexHolding $holding) => [
                'holding' => $holding,
                'company' => $holding->company,
                'weight' => $this->getCurrentWeight($holding),
            ])
            ->sortByDesc('weight')
            ->values()
            ->map(fn ($item, $index) => [
                'id' => $item['company']->id,
                'name' => $item['company']->name,
                'ticker' => $item['company']->ticker,
                'logo' => $item['company']->logo,
                'weight' => $item['weight'],
                'rank' => $index + 1,
                'index_holding_id' => $item['holding']->id,
            ]);

        return $rankedCompanies->toArray();
    }

    private function getCurrentWeight($indexHolding): float
    {
        return $indexHolding->marketData
            ->sortByDesc('date')
            ->first()?->weight ?? 0.0;
    }
}
