<?php

namespace App\Http\Controllers;

use App\Http\Services\WeightHistory\CountryWeightHistoryStrategy;
use App\Http\Services\WeightHistory\WeightHistoryService;
use App\Models\Country;
use Inertia\Inertia;

class CountryController extends Controller
{
    public function show(Country $country)
    {
        $country = Country::withStats()
            ->where('id', $country->id)
            ->firstOrFail();

        $weightHistoryService = new WeightHistoryService(new CountryWeightHistoryStrategy);
        $weightHistory = $weightHistoryService->getWeightHistory($country->id);

        $countryCompanies = $country
            ->companies()
            ->paginate(200);

        return inertia('Country/CountryShow', [
            'country' => $country,
            'weightHistory' => $weightHistory,
            'companies' => Inertia::merge($countryCompanies),
            'nextCompaniesPage' => request()->input('page', 1) + 1,
        ]);
    }
}
