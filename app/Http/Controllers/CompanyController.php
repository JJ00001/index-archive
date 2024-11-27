<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
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
        $companyMarketData = DB::select('
            SELECT date, weight
            FROM market_data
            WHERE company_id = ?
            ORDER BY date
        ', [$company->id]);

        $weightHistory = [
            'dates' => array_map(fn($data) => $data->date, $companyMarketData),
            'weights' => array_map(fn($data) => $data->weight, $companyMarketData),
        ];

        return inertia('Company/CompanyShow', [
            'company' => $company,
            'weightHistory' => $weightHistory,
        ]);
    }
}
