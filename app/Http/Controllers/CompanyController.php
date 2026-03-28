<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Inertia\Inertia;
use Inertia\Response;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $companies = QueryBuilder::for(Company::query())
            ->allowedSorts('name')
            ->paginate(10)
            ->withQueryString();

        $sort = request('sort');

        $currentSort = [
            'column' => ltrim($sort, '-'),
            'direction' => str_starts_with($sort, '-') ? 'desc' : 'asc',
        ];

        return inertia('Company/CompanyIndex', [
            'companies' => Inertia::scroll($companies),
            'sort' => $currentSort,
        ])->withViewData([
            'seo' => new SEOData(
                title: 'Browse companies',
                description: 'Explore companies that appear in tracked market indices and compare their sector, country, and exchange footprint.',
            ),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company): Response
    {
        $company->load([
            'country',
            'exchange',
            'sector',
        ]);

        return inertia('Company/CompanyShow', [
            'company' => $company,
        ])->withViewData([
            'seo' => $company->getDynamicSEOData(),
        ]);
    }
}
