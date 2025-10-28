<?php

namespace App\Http\Controllers;

use App\Http\Services\WeightHistory\SectorWeightHistoryStrategy;
use App\Models\Sector;
use Inertia\Inertia;

class SectorController extends Controller
{
    public function show(Sector $sector)
    {
        $sector = Sector::withStats()
            ->where('id', $sector->id)
            ->firstOrFail();

        // TODO: SectorWeightHistoryStrategy needs index context. For now, return empty data.
        $weightHistory = ['labels' => [], 'datasets' => []];

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
