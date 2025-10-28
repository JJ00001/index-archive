<?php

use App\Http\Controllers\Api\IndexHoldingController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::get('/indices/top', [IndexController::class, 'top'])->name('indices.top');
    Route::get('/index-holdings/{indexHolding}', [IndexHoldingController::class, 'show'])->name('index-holdings.show');
    Route::get('/companies/top', [CompanyController::class, 'top'])->name('companies.top');
});
