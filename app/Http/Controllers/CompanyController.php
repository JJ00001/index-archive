<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Date;
use Inertia\Inertia;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::activeConstituent()
            ->with([
                'country',
                'sector',
                'exchange',
            ])
            ->withLatestWeight()
            ->orderByDesc('latest_weight')
            ->paginate();

        return inertia('Company/CompanyIndex', [
            'companies' => Inertia::merge($companies),
            'nextPage' => request()->input('page', 1) + 1,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $companyMarketData = $company->marketDatas()->get();
        $weightHistory = [
            'dates' => $companyMarketData->map(fn($data) => Date::make($data->date)?->format('M Y'))->toArray(),
            'weights' => $companyMarketData->map(fn($data) => (float)$data->weight)->toArray()
        ];

        $company->load([
            'country',
            'exchange',
            'sector',
        ]);

        return inertia('Company/CompanyShow', [
            'company' => $company,
            'weightHistory' => $weightHistory,
        ]);
    }
}
