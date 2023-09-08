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
Route::group(['middleware' => 'auth:api', 'prefix' => 'users'], function () {
    //Route::get('/protected-endpoint', 'SecretController@index');
    //user update 
    Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
    //all user
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    //specif user by id
    Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
    //delete user by id 
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});
Route::group(['middleware' => 'auth:api', 'prefix' => 'locations'], function () {
    Route::get('/', [LocationController::class, 'index'])->name('locations.index');
    Route::get('/{id}', [LocationController::class, 'show'])->name('locations.show');
    Route::get('/{minLatitude}/{maxLatitude}/{minLongitude}/{maxLongitude}', [LocationController::class, 'get_cities_within_coordinates']);
    Route::post('/', [LocationController::class, 'store'])->name('locations.store');
    Route::put('/{id}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');
});

//unprotected enpoints 
Route::post('/users', [UserController::class, 'store'])->name('users.store');


Route::get('/cities/{minLatitude}/{maxLatitude}/{minLongitude}/{maxLongitude}', [LocationController::class, 'get_cities_within_coordinates']);
//all cities 
Route::get('city-locations', [LocationController::class, 'get_city']);
