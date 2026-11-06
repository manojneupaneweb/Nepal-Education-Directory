<?php

use App\Http\Controllers\Public\GeographyController;
use Illuminate\Support\Facades\Route;

/*
| Public API routes will be added when institution data is published.
| This file is registered with the /api prefix and the stateless API middleware.
*/

Route::get('/status', function () {
    return response()->json([
        'application' => config('app.name'),
        'status' => 'ok',
    ]);
})->name('api.status');

Route::get('/geography/provinces', [GeographyController::class, 'provinces'])->name('api.geography.provinces');
Route::get('/geography/provinces/{province}/districts', [GeographyController::class, 'districts'])->name('api.geography.districts');
Route::get('/geography/districts/{district}/municipalities', [GeographyController::class, 'municipalities'])->name('api.geography.municipalities');
Route::get('/geography/municipalities/{municipality}/wards', [GeographyController::class, 'wards'])->name('api.geography.wards');
