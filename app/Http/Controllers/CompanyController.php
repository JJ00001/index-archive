<?php

namespace App\Http\Controllers;

use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all()->load([
            'country',
            'sector',
            'exchange'
        ])->take(50);

        return inertia('Company/Index', [
            'companies' => $companies,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return inertia('Company/Show',
            ['company' => $company]);
    }
}
