<?php

namespace App\Http\Controllers;

use App\Http\Services\WeightHistory\SectorWeightHistoryStrategy;
use App\Http\Services\WeightHistory\WeightHistoryService;
use App\Models\Sector;

class SectorController extends Controller
{
    public function index()
    {
        $sectors = Sector::withStats()
            ->orderByDesc('weight')
            ->get();

        return inertia('Sector/SectorIndex', [
            'sectors' => $sectors
        ]);
    }

    public function show(Sector $sector)
    {
        $weightHistoryStrategy = new WeightHistoryService(new SectorWeightHistoryStrategy());
        $weightHistory = $weightHistoryStrategy->getWeightHistory($sector->id);

        return inertia('Sector/SectorShow', [
            'sector' => $sector,
            'weightHistory' => $weightHistory
        ]);
    }
}
