<?php

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\IndexHoldingController;
use App\Http\Controllers\Api\SectorController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::get('/indices/top', [IndexController::class, 'top'])->name('indices.top');
    Route::get('/index-holdings/{indexHolding}', [IndexHoldingController::class, 'show'])->name('index-holdings.show');
    Route::get('/indices/{index}/countries/{country}', [CountryController::class, 'show'])
         ->name('indices.countries.show');
    Route::get('/indices/{index}/sectors/{sector}', [SectorController::class, 'show'])->name('indices.sectors.show');
    Route::get('/companies/top', [CompanyController::class, 'top'])->name('companies.top');
});
