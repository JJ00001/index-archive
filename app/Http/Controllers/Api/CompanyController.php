<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;

class CompanyController extends Controller
{
    public function top()
    {
        $companies = Company::limit(5)->get(['id', 'name', 'ticker']);

        return response()->json($companies);
    }
}
