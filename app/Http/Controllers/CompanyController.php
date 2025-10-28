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
        $companies = Company::paginate(200);

        return inertia('Company/CompanyIndex', [
            'companies' => Inertia::merge($companies),
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

        $weightHistoryService = new WeightHistoryService(new CompanyWeightHistoryStrategy());
        $weightHistory = $weightHistoryService->getWeightHistory($company->id);

        return inertia('Company/CompanyShow', [
            'company' => $company,
            'weightHistory' => $weightHistory,
        ]);
    }
}
