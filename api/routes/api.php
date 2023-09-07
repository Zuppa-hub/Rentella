<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Route::get('city-locations', [LocationController::class, 'get_city']);
// protected endpoints
Route::group(['middleware' => 'auth:api'], function () {
    //Route::get('/protected-endpoint', 'SecretController@index');
    // more endpoints ...
    //city with latitude and logitude range 
    Route::get('/cities/{minLatitude}/{maxLatitude}/{minLongitude}/{maxLongitude}', [LocationController::class, 'get_cities_within_coordinates']);
    //all cities 
    Route::get('city-locations', [LocationController::class, 'get_city']);
});

//unprotected enpoints 
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

