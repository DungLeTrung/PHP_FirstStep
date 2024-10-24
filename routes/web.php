<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ProductsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [PagesController::class,'index']);

Route::group(['middleware' => ['auth']], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});

Route::group(['middleware' => ['auth', 'admin']], function () {

    Route::get('/users', [UsersController::class,'index'])->name('users');
    // Route::get('/users/age_filter', [UsersController::class, 'age_filter'])->name('uers.age_filter');
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UsersController::class, 'delete'])->name('users.delete');

    Route::get('/products', [ProductsController::class,'index'])->name('products');
    Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductsController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [ProductsController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductsController::class, 'delete'])->name('products.delete');
});


//AUTH
Route::get('/register', [AuthController::class,'index'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/verify-otp', [AuthController::class, 'showOtpVerificationForm'])->name('verify.otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::get('/send-otp', [AuthController::class, 'showSendOTPForm'])->name('send.OTP_view');
Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('send.OTP');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/send-otp-forgot-password', [AuthController::class, 'showSendOTPForgotPassForm'])->name('send_forgot_password.OTP');
Route::post('/send-otp-forgot-password', [AuthController::class, 'sendOtpForgotPassword'])->name('send_forgot_password.OTP');

Route::get('/verify-otp-forgot-password', [AuthController::class, 'showOtpVerificationForgotPasswordForm'])->name('verify_forgot_password.otp');
Route::post('/verify-otp-forgot-password', [AuthController::class, 'verifyOtpForPasswordReset'])->name('verify_forgot_password.otp');;

Route::get('/forgot-password/reset', [AuthController::class, 'showPasswordResetForm'])->name('password_reset.otp');
Route::post('/forgot-password/reset', [AuthController::class, 'updatePassword'])->name('password_reset_password.otp');


