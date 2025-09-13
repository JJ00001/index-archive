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

        $weightHistoryService = new WeightHistoryService(new CompanyWeightHistoryStrategy());
        $multipleWeightHistory = $weightHistoryService->getMultipleWeightHistory($companies->take(10));

        return inertia('Company/CompanyIndex', [
            'companies' => Inertia::merge($companies),
            'nextPage' => request()->input('page', 1) + 1,
            'multipleWeightHistory' => $multipleWeightHistory,
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

    public function top()
    {
        $companies = Company::limit(5)
                            ->get(['id', 'name', 'ticker']);

        return response()->json($companies);
    }
}
