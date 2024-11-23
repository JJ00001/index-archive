<?php

namespace App\Http\Controllers;

use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::withStats()
            ->orderByDesc('weight')
            ->get();

        return inertia('Country/CountryIndex', [
            'countries' => $countries,
        ]);
    }
}
