<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\SectorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/companies');

Route::resource('companies', CompanyController::class)->only(['index', 'show']);
Route::resource('countries', CountryController::class)->only(['index', 'show']);
Route::resource('sectors', SectorController::class)->only(['index', 'show']);

Route::get('/analytics.js', function () {
    return response()->file(public_path('js/plausible.js'), [
        'Content-Type' => 'application/javascript'
    ]);
});

Route::post('/analytics-event', function (Request $request) {
    $dataDecoded = json_decode($request->getContent(), true) ?? [];

    $data = [
        'n' => $dataDecoded['n'] ?? 'pageview',
        'u' => $dataDecoded['u'] ?? url('/'),
        'd' => $dataDecoded['d'] ?? parse_url(config('app.url'), PHP_URL_HOST),
        'r' => $dataDecoded['r'] ?? '',
        'p' => $dataDecoded['p'] ?? null,
    ];

    return Http::post('https://plausible.io/api/event', $data);
});
