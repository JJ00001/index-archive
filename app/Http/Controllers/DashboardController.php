<?php

namespace App\Http\Controllers;

use App\Models\Index;
use Inertia\Response;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $indices = Index::withCount('indexHoldings')
            ->with('indexProvider:id,name')
            ->get();

        return inertia('Dashboard', [
            'indices' => $indices,
        ])->withViewData([
            'seo' => new SEOData(
                title: 'Track global index composition',
                description: 'IndexArchive tracks how major market indices change over time across countries, sectors, and companies.',
            ),
        ]);
    }
}
