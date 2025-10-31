<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\WeightHistory\IndexHoldingWeightHistoryStrategy;
use App\Http\Services\WeightHistory\WeightHistoryService;
use App\Models\IndexHolding;

class IndexHoldingController extends Controller
{

    public function show(IndexHolding $indexHolding)
    {
        $indexHolding->load([
            'company.country',
            'company.exchange',
            'company.sector',
        ]);

        $weightHistoryService = new WeightHistoryService(new IndexHoldingWeightHistoryStrategy());
        $weightHistory        = $weightHistoryService->getWeightHistory($indexHolding->company_id,
            $indexHolding->index_id);

        $indexHolding->weight_history = $weightHistory;

        return response()->json([
            'props' => [
                'indexHolding' => $indexHolding,
            ],
        ]);
    }

}
