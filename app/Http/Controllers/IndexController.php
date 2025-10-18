<?php

namespace App\Http\Controllers;

use App\Http\Resources\IndexHoldingCompanyCollection;
use App\Http\Resources\IndexHoldingCountryCollection;
use App\Http\Resources\IndexHoldingSectorCollection;
use App\Models\Index;

class IndexController extends Controller
{
    public function index()
    {
        $indices = Index::withCount('indexHoldings')->get();

        return inertia('Index/IndexIndex', [
            'indices' => $indices,
        ]);
    }

    public function show(Index $index)
    {
        $index->load(['indexProvider']);

        $indexHoldingsSortedByWeight = $index->latestMarketData()
            ->with('indexHolding')
            ->get()
            ->sortByDesc('weight')
            ->take(100)
            ->map(fn ($marketData) => $marketData->indexHolding)
            ->values();

        $companies = new IndexHoldingCompanyCollection($indexHoldingsSortedByWeight);

        $sectors = new IndexHoldingSectorCollection($index);

        $countries = new IndexHoldingCountryCollection($index);

        $stats = [
            [
                'title' => 'Holdings',
                'value' => $index->indexHoldings()->count(),
            ],
            [
                'title' => 'Provider',
                'value' => $index->indexProvider->name,
            ],
            [
                'title' => 'Currency',
                'value' => $index->currency,
            ],
        ];

        return inertia('Index/IndexShow', [
            'index' => $index,
            'stats' => $stats,
            'companies' => $companies,
            'sectors' => $sectors,
            'countries' => $countries,
        ]);
    }

    public function top()
    {
        $indices = Index::limit(5)->get(['id', 'name']);

        return response()->json($indices);
    }
}
