<?php

namespace App\Http\Controllers;

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
}
