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
            'exchange',
            'marketDatas' => function ($query) {
                $query->latest()->limit(1);
            }
        ]);

        return inertia('Company/Index', [
            'companies' => $companies,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
