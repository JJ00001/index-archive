<?php

namespace App\Http\Controllers;

use App\Http\Resources\IndexHoldingCompanyCollection;
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

        $companies = new IndexHoldingCompanyCollection($index->indexHoldings->take(100));

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
        ]);
    }

    public function top()
    {
        $indices = Index::limit(5)->get(['id', 'name']);

        return response()->json($indices);
    }

}
