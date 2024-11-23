<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard');
});

Route::resource('companies', CompanyController::class)->only(['index', 'show']);
Route::resource('countries', CountryController::class)->only(['index']);
