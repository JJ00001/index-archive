<?php

namespace App\Http\Controllers;

use App\Http\Services\WeightHistory\SectorWeightHistoryStrategy;
use App\Http\Services\WeightHistory\WeightHistoryService;
use App\Models\Sector;
use Inertia\Inertia;

class SectorController extends Controller
{
    public function index()
    {
        $sectors = Sector::withStats()
            ->orderByDesc('weight')
            ->get();

        $weightHistoryStrategy = new WeightHistoryService(new SectorWeightHistoryStrategy());
        $multipleWeightHistory = $weightHistoryStrategy->getMultipleWeightHistory($sectors);

        return inertia('Sector/SectorIndex', [
            'sectors' => $sectors,
            'multipleWeightHistory' => $multipleWeightHistory,
        ]);
    }

    public function show(Sector $sector)
    {
        $sector = Sector::withStats()
            ->where('id', $sector->id)
            ->firstOrFail();

        $weightHistoryStrategy = new WeightHistoryService(new SectorWeightHistoryStrategy());
        $weightHistory = $weightHistoryStrategy->getWeightHistory($sector->id);

        $sectorCompanies = $sector
            ->companies()
            ->paginate(200);

        return inertia('Sector/SectorShow', [
            'sector' => $sector,
            'weightHistory' => $weightHistory,
            'companies' => Inertia::merge($sectorCompanies),
            'nextCompaniesPage' => request()->input('page', 1) + 1,
        ]);
    }
}
