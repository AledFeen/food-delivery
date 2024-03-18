<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {

        Route::get('user/check/store', [StoreController::class, 'checkStoreExistence']);
        Route::group(['middleware' => 'isStore'], function () {
            Route::get('user/store', [StoreController::class, 'selectUserStore']);
        });

        Route::get('user/check/admin', [AdminController::class, 'checkAdminExistence']);
        Route::group(['middleware' => 'isAdmin'], function () {
            Route::get('user/admin', [AdminController::class, 'selectAdmin']);
        });

    });
});

