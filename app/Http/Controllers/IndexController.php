<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Http\Resources\IndexHoldingCountryCollection;
use App\Http\Resources\IndexHoldingSectorCollection;
use App\Models\CurrentIHMarketData;
use App\Models\Index;
use Inertia\Inertia;
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
        $index->load(['indexProvider'])->loadCount('indexHoldings');

        $currentIndexHoldings = CurrentIHMarketData::query()
                                                   ->forIndex($index->id)
                                                   ->with('company')
                                                   ->orderByDesc('weight')
                                                   ->paginate(20, pageName: 'companies')
                                                   ->withQueryString();

        $sectors = new IndexHoldingSectorCollection($index);

        $countries = new IndexHoldingCountryCollection($index);

        $activities = ActivityResource::collection(
            Activity::query()
                ->where('properties->index_id', $index->id)
                ->orderBy('properties->date', 'desc')
                ->limit(50)
                ->get()
        );

        return inertia('Index/IndexShow', [
            'index' => $index,
            'stats' => $index->stats(),
            'companies' => Inertia::scroll($currentIndexHoldings),
            'sectors' => $sectors,
            'countries' => $countries,
            'activities' => $activities,
        ]);
    }
}
