<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\SectorController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/companies');

Route::resource('companies', CompanyController::class)->only(['index', 'show']);
Route::resource('countries', CountryController::class)->only(['index', 'show']);
Route::resource('sectors', SectorController::class)->only(['index', 'show']);
