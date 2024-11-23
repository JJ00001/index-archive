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
        $companies = Company::with([
                'country',
                'sector',
                'exchange',
            ])
            ->withStats()
            ->orderBy('rank')
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
        $company = Company::withStats()
            ->where('id', $company->id)
            ->with([
                'country',
                'exchange',
                'sector',
            ])
            ->firstOrFail();

        // TODO refactor to a service
        $companyMarketData = $company->marketDatas()->get();
        $weightHistory = [
            'dates' => $companyMarketData->map(fn($data) => Date::make($data->date)?->format('M Y'))->toArray(),
            'weights' => $companyMarketData->map(fn($data) => (float)$data->weight)->toArray()
        ];

        return inertia('Company/CompanyShow', [
            'company' => $company,
            'weightHistory' => $weightHistory,
        ]);
    }
}
