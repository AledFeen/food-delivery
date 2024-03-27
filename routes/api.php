<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\StoreCategoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreTypeController;
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

    Route::get('categories/get', [StoreCategoryController::class, 'getCategories']);
    Route::get('types/get', [StoreTypeController::class, 'getTypes']);

    Route::group(['middleware' => 'auth:sanctum'], function () {

        Route::get('user/check/store', [StoreController::class, 'checkStoreExistence']);
        Route::group(['middleware' => 'isStore'], function () {
            Route::get('/user/store', [StoreController::class, 'selectUserStore']);
            Route::post('/user/store/updateProfile', [StoreController::class, 'updateStoreProfile']);
            Route::get('/user/store/categories', [CategoryController::class, 'getCategories']);
            Route::post('/store/add/mainCategory',[CategoryController::class, 'addMainCategory']);
            Route::post('/store/add/subCategory',[CategoryController::class, 'addSubCategory']);
        });

        Route::get('user/check/admin', [AdminController::class, 'checkAdminExistence']);
        Route::group(['middleware' => 'isAdmin'], function () {
            Route::get('/user/admin', [AdminController::class, 'selectAdmin']);
            Route::get('/admin/users/get', [AdminController::class, 'getUsers']);
            Route::post('/admin/users/add', [AdminController::class, 'addStore']);
        });

        Route::get('user/check/courier', [CourierController::class, 'checkCourierExistence']);
        Route::group(['middleware' => 'isCourier'], function () {
            Route::get('user/courier', [CourierController::class, 'selectCourier']);
        });

    });
});

