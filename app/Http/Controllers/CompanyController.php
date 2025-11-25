<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $company->load([
            'country',
            'exchange',
            'sector',
        ]);

        return inertia('Company/CompanyShow', [
            'company' => $company,
        ]);
    }
}
