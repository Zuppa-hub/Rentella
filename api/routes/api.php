<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BeachController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\BeachPictureController;
use App\Http\Controllers\BeachTypeController;
use App\Http\Controllers\BeachZonesController;
use App\Http\Controllers\OpeningDatesController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\OwnersController;
use App\Http\Controllers\PricesController;
use App\Http\Controllers\UmbrellasController;


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

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */
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
Route::group(['middleware' => 'auth:api', 'prefix' => 'beaches'], function () {
    Route::get('/', [BeachController::class, 'index'])->name('beaches.index');
    Route::get('/{id}', [BeachController::class, 'show'])->name('beaches.show');
    Route::post('/', [BeachController::class, 'store'])->name('beaches.store');
    Route::put('/{id}', [BeachController::class, 'update'])->name('beaches.update');
    Route::delete('/{id}', [BeachController::class, 'destroy'])->name('beaches.destroy');
});
Route::group(['middleware' => 'auth:api', 'prefix' => 'beach-pictures'], function () {
    Route::get('/', [BeachPictureController::class, 'index'])->name('beach-pictures.index');
    Route::get('/{id}', [BeachPictureController::class, 'show'])->name('beach-pictures.show');
    Route::post('/', [BeachPictureController::class, 'store'])->name('beach-pictures.store');
    Route::put('/{id}', [BeachPictureController::class, 'update'])->name('beach-pictures.update');
    Route::delete('/{id}', [BeachPictureController::class, 'destroy'])->name('beach-pictures.destroy');
});
Route::group(['middleware' => 'auth:api', 'prefix' => 'beach-types'], function () {
    Route::get('/', [BeachTypeController::class, 'index'])->name('beach-types.index');
    Route::get('/{id}', [BeachTypeController::class, 'show'])->name('beach-types.show');
    Route::post('/', [BeachTypeController::class, 'store'])->name('beach-types.store');
    Route::put('/{id}', [BeachTypeController::class, 'update'])->name('beach-types.update');
    Route::delete('/{id}', [BeachTypeController::class, 'destroy'])->name('beach-types.destroy');
});
Route::group(['middleware' => 'auth:api', 'prefix' => 'beach-zones'], function () {
    Route::get('/', [BeachZonesController::class, 'index'])->name('beach-zone.index');
    Route::get('/{id}', [BeachZonesController::class, 'show'])->name('beach-zone.show');
    Route::post('/', [BeachZonesController::class, 'store'])->name('beach-zone.store');
    Route::put('/{id}', [BeachZonesController::class, 'update'])->name('beach-zone.update');
    Route::delete('/{id}', [BeachZonesController::class, 'destroy'])->name('beach-zone.destroy');
});
Route::group(['middleware' => 'auth:api', 'prefix' => 'opening-dates'], function () {
    Route::get('/', [OpeningDatesController::class, 'index'])->name('opening-dates.index');
    Route::get('/{id}', [OpeningDatesController::class, 'show'])->name('opening-dates.show');
    Route::post('/', [OpeningDatesController::class, 'store'])->name('opening-dates.store');
    Route::put('/{id}', [OpeningDatesController::class, 'update'])->name('opening-dates.update');
    Route::delete('/{id}', [OpeningDatesController::class, 'destroy'])->name('opening-dates.destroy');
});
Route::group(['middleware' => 'auth:api', 'prefix' => 'orders'], function () {
    Route::get('/', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('/{id}', [OrdersController::class, 'show'])->name('orders.show');
    Route::post('/', [OrdersController::class, 'store'])->name('orders.store');
    Route::put('/{id}', [OrdersController::class, 'update'])->name('orders.update');
    Route::delete('/{id}', [OrdersController::class, 'destroy'])->name('orders.destroy');
});
Route::group(['middleware' => 'auth:api', 'prefix' => 'owners'], function () {
    Route::get('/', [OwnersController::class, 'index'])->name('owners.index');
    Route::get('/{id}', [OwnersController::class, 'show'])->name('owners.show');
    Route::post('/', [OwnersController::class, 'store'])->name('owners.store');
    Route::put('/{id}', [OwnersController::class, 'update'])->name('owners.update');
    Route::delete('/{id}', [OwnersController::class, 'destroy'])->name('owners.destroy');
});
Route::group(['middleware' => 'auth:api', 'prefix' => 'prices'], function () {
    Route::get('/', [PricesController::class, 'index'])->name('prices.index');
    Route::get('/{id}', [PricesController::class, 'show'])->name('prices.show');
    Route::post('/', [PricesController::class, 'store'])->name('prices.store');
    Route::put('/{id}', [PricesController::class, 'update'])->name('prices.update');
    Route::delete('/{id}', [PricesController::class, 'destroy'])->name('prices.destroy');
});
Route::group(['middleware' => 'auth:api', 'prefix' => 'umbrellas'], function () {
    Route::get('/', [UmbrellasController::class, 'index'])->name('umbrellas.index');
    Route::get('/{id}', [UmbrellasController::class, 'show'])->name('umbrellas.show');
    Route::post('/', [UmbrellasController::class, 'store'])->name('umbrellas.store');
    Route::put('/{id}', [UmbrellasController::class, 'update'])->name('umbrellas.update');
    Route::delete('/{id}', [UmbrellasController::class, 'destroy'])->name('umbrellas.destroy');
});
//unprotected enpoints 
Route::get('/health', [HealthController::class, 'index'])->name('health');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
