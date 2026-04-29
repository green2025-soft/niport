<?php

use Illuminate\Support\Facades\Route;


use Modules\Niport\Http\Controllers\Api\DivisionController;
use Modules\Niport\Http\Controllers\Api\DistrictController;
use Modules\Niport\Http\Controllers\Api\UpazilaController;
use Modules\Niport\Http\Controllers\Api\UnionController;
use Modules\Niport\Http\Controllers\Api\ReferenceController;
use Modules\Niport\Http\Controllers\Api\SurveyController;


Route::middleware(['auth:sanctum'])->prefix('v1/niport')->name('niport.')->group(function () {
// Route::middleware(['auth:sanctum', 'admin'])->prefix('v1/niport')->name('niport')->group(function () {
    Route::apiResource('division', DivisionController::class);
    Route::apiResource('district', DistrictController::class);
    Route::apiResource('upazila', UpazilaController::class);
    Route::apiResource('union', UnionController::class);


    Route::controller(ReferenceController::class)->group(function () {
        Route::get('/categories/{category}/faculties', 'faculties');
        Route::get('/departments', 'departments');
        Route::get('/designations', 'designations');
        Route::get('/diseases', 'diseases');
        Route::get('/conditions', 'conditions');
        Route::get('/list-dependencies', 'listDependencies');
        Route::get('/create-dependencies', 'createDependencies');
    });

    Route::resource('surveys', SurveyController::class);
});
