<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\WeightHistory\CountryWeightHistoryStrategy;
use App\Http\Services\WeightHistory\WeightHistoryService;
use App\Models\Country;
use App\Models\Index;

class CountryController extends Controller
{

    public function show(Index $index, Country $country)
    {
        $country->load(['companies']);

        $weightHistoryService = new WeightHistoryService(new CountryWeightHistoryStrategy());
        $weightHistory        = $weightHistoryService->getWeightHistory($country->id, $index->id);

        $country->weight_history = $weightHistory;

        return response()->json([
            'props' => [
                'country' => $country,
            ],
        ]);
    }

}
