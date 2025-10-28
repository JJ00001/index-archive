<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\WeightHistory\CompanyWeightHistoryStrategy;
use App\Http\Services\WeightHistory\WeightHistoryService;
use App\Models\IndexHolding;

class IndexHoldingController extends Controller
{
    public function show(IndexHolding $indexHolding)
    {
        $company = $indexHolding->company;
        $company->load([
            'country',
            'exchange',
            'sector',
        ]);

        $weightHistoryService = new WeightHistoryService(new CompanyWeightHistoryStrategy);
        $weightHistory = $weightHistoryService->getWeightHistory($company->id, $indexHolding->index_id);

        return response()->json([
            'props' => [
                'index' => $indexHolding->index,
                'company' => $company,
                'weightHistory' => $weightHistory,
            ],
        ]);
    }
}
