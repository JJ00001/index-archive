<?php

namespace App\Http\Controllers;

use App\Models\Index;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $indices = Index::withCount('indexHoldings')
                        ->with('indexProvider:id,name')
                        ->get();

        return inertia('Dashboard', [
            'indices' => $indices,
        ]);
    }
}
