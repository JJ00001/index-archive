<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\SectorController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/indices');

Route::resource('companies', CompanyController::class)->only(['index', 'show']);
Route::resource('countries', CountryController::class)->only(['index', 'show']);
Route::resource('sectors', SectorController::class)->only(['index', 'show']);
Route::resource('indices', IndexController::class)->only(['index', 'show']);

Route::prefix('api')->name('api.')->group(function () {
    Route::get('/indices/top', [IndexController::class, 'top'])->name('indices.top');
    Route::get('/companies/top', [CompanyController::class, 'top'])->name('companies.top');
});
