<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);

Route::resource('companies', CompanyController::class)->only(['index', 'show']);
Route::resource('indices', IndexController::class)->only(['index', 'show']);
