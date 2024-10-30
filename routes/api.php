<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
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
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-account', [AuthController::class, 'verifyAccount']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    //User
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::put('/edit-profile', [UserController::class, 'editProfile']);
    Route::get('/detail-profile', [UserController::class, 'detailProfile']);
    //Order
    Route::post('/orders', [OrderController::class, 'createOrder']);
    Route::get('/orders', [OrderController::class, 'getOrder']);
    Route::get('/orders/{id}', [OrderController::class, 'getOrderById']);

    Route::middleware('admin.api')->group(function () {
        //User
        Route::get('/users', [UserController::class, 'getAllUsers']);
        Route::get('/users/{id}', [UserController::class, 'getUserById']);
        Route::post('/users', [UserController::class, 'createUser']);

        //Categories
        Route::get('/categories', [CategoryController::class, 'getAllCategories']);
        Route::post('/categories', [CategoryController::class, 'createCategory']);
        Route::put('/categories/{id}', [CategoryController::class, 'updateCategory']);
        Route::delete('/categories/{id}', [CategoryController::class, 'deleteCategory']);
        Route::get('/categories/{id}', [CategoryController::class, 'getCategoryById']);

        //Products
        Route::get('/products', [ProductController::class, 'getAllProducts']);
        Route::post('/products', [ProductController::class, 'createProduct']);
        Route::put('/products/{id}', [ProductController::class, 'updateProduct']);
        Route::delete('/products/{id}', [ProductController::class, 'deleteProduct']);
        Route::get('/products/{id}', [ProductController::class, 'getProductById']);

        //Orders
        Route::get('/order-filter', [OrderController::class, 'filterOrders']);
    });
});

