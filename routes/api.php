<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
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

    Route::get('/categories/get', [StoreCategoryController::class, 'getCategories']);
    Route::get('/types/get', [StoreTypeController::class, 'getTypes']);
    Route::get('/cities', [CityController::class, 'getCities']);
    Route::get('/stores', [StoreController::class, 'getStoresByCityId']);
    Route::get('/store', [StoreController::class, 'getStoreById']);
    Route::get('/store/categories', [CategoryController::class, 'getCategoriesByStoreId']);
    Route::get('/store/category/products', [ProductController::class, 'getProductsByCategoryId']);
    Route::get('/home/store/product/categories', [ProductController::class, 'getProductCategoriesByStoreId']);


    Route::group(['middleware' => 'auth:sanctum'], function () {

        Route::post('/checkout', [OrderController::class, 'checkout']);
        Route::get('/user/orders', [OrderController::class, 'getUserOrders']);
        Route::post('/user/orders/submit', [OrderController::class, 'submitDelivery']);
        Route::get('/user/check/store', [StoreController::class, 'checkStoreExistence']);

        Route::group(['middleware' => 'isStore'], function () {
            Route::get('/user/store', [StoreController::class, 'selectUserStore']);
            Route::post('/user/store/updateProfile', [StoreController::class, 'updateStoreProfile']);
            Route::get('/user/store/categories', [CategoryController::class, 'getCategories']);
            Route::get('/store/cities', [CityController::class, 'getStoreCitiesList']);
            Route::delete('/store/city/{id}', [CityController::class, 'deleteCityFromList']);
            Route::post('/store/add/city', [CityController::class, 'addCityToStore']);
            Route::post('/store/add/mainCategory',[CategoryController::class, 'addMainCategory']);
            Route::post('/store/add/subCategory',[CategoryController::class, 'addSubCategory']);
            Route::post('/store/add/product',[ProductController::class, 'addProduct']);
            Route::post('/store/update/product',[ProductController::class, 'updateProduct']);
            Route::get('/store/product/categories', [ProductController::class, 'getProductCategories']);
            Route::post('/store/product/add/category', [ProductController::class, 'addProductCategory']);
            Route::post('/store/product/category/add/item', [ProductController::class, 'addItemToCategory']);
            Route::delete('/store/product/category/delete/item', [ProductController::class, 'deleteItemFromProductCategory']);
            Route::delete('/store/product/delete/category', [ProductController::class, 'deleteCategoryFromProduct']);
            Route::delete('/store/delete/product', [ProductController::class, 'deleteProduct']);
            Route::delete('/store/delete/subCategory', [CategoryController::class, 'deleteSubcategory']);
            Route::delete('/store/delete/category', [CategoryController::class, 'deleteCategory']);
        });

        Route::get('/user/check/admin', [AdminController::class, 'checkAdminExistence']);

        Route::group(['middleware' => 'isAdmin'], function () {
            Route::get('/user/admin', [AdminController::class, 'selectAdmin']);
            Route::get('/admin/users/get', [AdminController::class, 'getUsers']);
            Route::post('/admin/users/add', [AdminController::class, 'addStore']);
            Route::delete('/admin/store/delete', [AdminController::class, 'deleteStore']);
            Route::post('/admin/courier/add', [AdminController::class, 'addCourier']);
        });

        Route::get('/user/check/courier', [CourierController::class, 'checkCourierExistence']);

        Route::group(['middleware' => 'isCourier'], function () {
            Route::get('/user/courier', [CourierController::class, 'selectCourier']);
            Route::get('/courier/availableOrders', [OrderController::class, 'getAvailableOrders']);
            Route::get('/courier/currentOrder', [OrderController::class, 'getCurrentOrderForCourier']);
            Route::post('/courier/selectOrder', [OrderController::class, 'selectOrderForDelivery']);
        });

    });
});

