<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TripController;




Route::post('/login', [LoginController::class, 'signin']);
Route::post('/login/varification', [LoginController::class, 'varifySignin']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/driver', [DriverController::class, 'show']);
    Route::post('/driver', [DriverController::class, 'update']);

    Route::post('/trip', [TripController::class, 'store']);
    Route::get('/trip/{trip}', [TripController::class, 'show']);
    Route::post('/trip/{trip}/accept', [TripController::class, 'acceptTrip']);
    Route::post('/trip/{trip}/start', [TripController::class, 'startTrip']);
    Route::post('/trip/{trip}/end', [TripController::class, 'endTrip']);
    Route::post('/trip/{trip}/location', [TripController::class, 'updateLocation']);
});