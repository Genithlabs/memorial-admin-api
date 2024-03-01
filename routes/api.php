<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/user')->group(function() {
    Route::post('register', [AuthController::class, 'register'])->name('user.register');
    Route::post('login', [AuthController::class, 'login'])->name('user.login');
    Route::post('findId', [AuthController::class, 'findId'])->name('user.findId');
    Route::post('forgot_password', [AuthController::class, 'forgotPassword'])->name('user.forgetPassword');
    Route::post('reset_password', [AuthController::class, 'resetPassword'])->name('user.resetPassword');
});
