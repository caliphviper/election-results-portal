<?php

use App\Http\Controllers\ResultLookupController;
use App\Http\Controllers\LgaSummaryController;
use App\Http\Controllers\NewPollingUnitController;

Route::get('/', function () {
    return view('home');
});

Route::get('/results/new-polling-unit', [NewPollingUnitController::class, 'create'])->name('results.new-polling-unit');
Route::post('/results/new-polling-unit', [NewPollingUnitController::class, 'store'])->name('results.new-polling-unit.store');
Route::get('/results/lga-summary', [LgaSummaryController::class, 'index'])->name('results.lga-summary');
Route::get('/api/lga-summary/{lga}', [LgaSummaryController::class, 'summaryByLga']);
Route::get('/results/lookup', [ResultLookupController::class, 'index'])->name('results.lookup');
Route::get('/api/lgas/{state}', [ResultLookupController::class, 'lgasByState']);
Route::get('/api/wards/{lga}', [ResultLookupController::class, 'wardsByLga']);
Route::get('/api/polling-units/{ward}', [ResultLookupController::class, 'pollingUnitsByWard']);
Route::get('/api/polling-unit-results/{pollingUnit}', [ResultLookupController::class, 'resultsByPollingUnit']);