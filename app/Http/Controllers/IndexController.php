<?php

namespace App\Http\Controllers;

use App\Models\Index;

class IndexController extends Controller
{

    public function index()
    {
        $indices = Index::all();

        return inertia('Index/IndexIndex', [
            'indices' => $indices,
        ]);
    }

    public function show(Index $index)
    {
        $index->load(['indexProvider', 'indexHoldings']);

        $stats = [
            [
                'title' => 'Holdings',
                'value' => $index->indexHoldings->count(),
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
        ]);
    }

    public function top()
    {
        $indices = Index::limit(5)->get(['id', 'name']);

        return response()->json($indices);
    }

}
