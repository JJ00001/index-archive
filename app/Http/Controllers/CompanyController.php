<?php

namespace App\Http\Controllers;

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

        return inertia('Company/CompanyShow', [
            'company' => $company,
        ]);
    }
}
