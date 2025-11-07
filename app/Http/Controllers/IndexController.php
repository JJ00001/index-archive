<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Http\Resources\IndexHoldingCompanyCollection;
use App\Http\Resources\IndexHoldingCountryCollection;
use App\Http\Resources\IndexHoldingSectorCollection;
use App\Models\Index;
use Spatie\Activitylog\Models\Activity;

class IndexController extends Controller
{
    public function index()
    {
        $indices = Index::withCount('indexHoldings')
                        ->with('indexProvider:id,name')
                        ->get();

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
            ->take(50)
            ->map(fn ($marketData) => $marketData->indexHolding)
            ->values();

        $companies = new IndexHoldingCompanyCollection($indexHoldingsSortedByWeight);

        $sectors = new IndexHoldingSectorCollection($index);

        $countries = new IndexHoldingCountryCollection($index);

        $activities = ActivityResource::collection(
            Activity::query()
                ->where('properties->index_id', $index->id)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
        );

        return inertia('Index/IndexShow', [
            'index' => $index,
            'stats' => $index->stats(),
            'companies' => $companies,
            'sectors' => $sectors,
            'countries' => $countries,
            'activities' => $activities,
        ]);
    }
}
