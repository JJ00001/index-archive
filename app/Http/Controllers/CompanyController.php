<?php

namespace App\Http\Controllers;

use App\Http\Services\WeightHistory\CompanyWeightHistoryStrategy;
use App\Http\Services\WeightHistory\WeightHistoryService;
use App\Models\Company;
use Inertia\Inertia;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::withStats()
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

        $weightHistoryService = new WeightHistoryService(new CompanyWeightHistoryStrategy());
        $weightHistory = $weightHistoryService->getWeightHistory($company->id);

        return inertia('Company/CompanyShow', [
            'company' => $company,
            'weightHistory' => $weightHistory,
        ]);
    }
}
