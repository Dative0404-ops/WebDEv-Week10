<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Home dashboard (friendly overview of the data).
Route::get('/', [ReportController::class, 'home'])->name('home');

// The assignment report page with the seven required queries.
Route::get('/report', [ReportController::class, 'index'])->name('report');
