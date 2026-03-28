<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Http\Resources\IndexHoldingCountryCollection;
use App\Http\Resources\IndexHoldingSectorCollection;
use App\Models\CurrentIHMarketData;
use App\Models\Index;
use Inertia\Inertia;
use Inertia\Response;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\QueryBuilder;

class IndexController extends Controller
{

    public function index(): Response
    {
        $indices = Index::withCount('indexHoldings')
            ->with('indexProvider:id,name')
            ->get();

        return inertia('Index/IndexIndex', [
            'indices' => $indices,
        ])->withViewData([
            'seo' => new SEOData(
                title: 'Browse indices',
                description: 'Explore tracked market indices and compare their providers, constituents, sector weights, and country exposure.',
            ),
        ]);
    }

    public function show(Index $index): Response
    {
        $index->load(['indexProvider'])->loadCount('indexHoldings');

        $currentIndexHoldings = QueryBuilder::for(
            CurrentIHMarketData::query()
                ->forIndex($index->id)
                ->with('company')
                ->leftJoin('companies', 'companies.id', '=',
                    'current_index_holding_market_data.company_id')
                ->select('current_index_holding_market_data.*'))
                                            ->defaultSort('-weight')
                                            ->allowedSorts([
                                                'weight',
                                                'companies.name',
                                            ])
                                            ->paginate(20, pageName: 'companies')
                                            ->withQueryString();

        $sort = request('sort');
        $resolvedSort = $sort ?? '-weight';

        $currentSort = [
            'column' => ltrim($resolvedSort, '-'),
            'direction' => str_starts_with($resolvedSort, '-') ? 'desc' : 'asc',
        ];

        $sectors = new IndexHoldingSectorCollection($index);

        $countries = new IndexHoldingCountryCollection($index);

        $activities = ActivityResource::collection(
            Activity::query()
                ->where('properties->index_id', $index->id)
                ->orderBy('properties->date', 'desc')
                ->limit(50)
                ->get()
        )->resolve();

        return inertia('Index/IndexShow', [
            'index' => $index,
            'stats' => $index->stats(),
            'companies' => Inertia::scroll($currentIndexHoldings),
            'sort' => $currentSort,
            'sectors' => $sectors,
            'countries' => $countries,
            'activities' => $activities,
        ])->withViewData([
            'seo' => $index->getDynamicSEOData(),
        ]);
    }
}
