<?php

use Illuminate\Support\Facades\Route;

use Modules\Core\Http\Controllers\NewAllRoutePermissionController;

Route::group(['prefix'=>'core'], function () {
//    dd('ok');
});

Route::controller(NewAllRoutePermissionController::class)->group(function () {
    Route::get('new-all-route-permission','store');
    Route::get('all-routes', 'getAllRoutes');
});

Route::get('/login', function () {
    return response()->json([
        'message' => 'Unauthenticated'
    ], 401);
})->name('login');
