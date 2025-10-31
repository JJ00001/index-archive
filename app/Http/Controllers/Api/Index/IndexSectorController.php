<?php

namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Controller;
use App\Http\Services\WeightHistory\SectorWeightHistoryStrategy;
use App\Http\Services\WeightHistory\WeightHistoryService;
use App\Models\Index;
use App\Models\Sector;

class IndexSectorController extends Controller
{

    public function show(Index $index, Sector $sector)
    {
        $sector = Sector::query()
                        ->with('companies')
                        ->withStatsInIndex($index)
                        ->findOrFail($sector->id);

        $weightHistoryService = new WeightHistoryService(new SectorWeightHistoryStrategy());
        $weightHistory        = $weightHistoryService->getWeightHistory($sector->id, $index->id);

        $sector->weight_history = $weightHistory;

        return response()->json([
            'props' => [
                'sector' => $sector,
            ],
        ]);
    }

}
