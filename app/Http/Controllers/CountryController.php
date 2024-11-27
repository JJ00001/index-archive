<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Support\Facades\DB;

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

    public function show(Country $country)
    {
        $countryId = $country->id;

        // TODO Refactor to service
        $query = "
            SELECT
              market_data.date,
              SUM(market_data.weight) AS weight
            FROM
              market_data
              JOIN companies ON companies.id = market_data.company_id
            WHERE
              companies.country_id = ?
            GROUP BY
              market_data.date
            ORDER BY
              market_data.date
        ";

        $countryMarketData = DB::select($query, [$countryId]);

        $weightHistory = [
            'dates' => array_map(fn($data) => $data->date, $countryMarketData),
            'weights' => array_map(fn($data) => $data->weight, $countryMarketData),
        ];

        return inertia('Country/CountryShow', [
            'country' => $country,
            'weightHistory' => $weightHistory,
        ]);
    }
}
