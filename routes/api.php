<?php

use App\Http\Controllers\Api\v1\Admin\User\UserController;
use App\Http\Controllers\Api\v1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->name('v1.authentication.')->group(function (){
    // login & register route
    Route::post('login-register', [AuthController::class, 'login'])->name('login');
    Route::post('check-otp', [AuthController::class, 'checkOtp'])->name('check.otp');

    // admin routes
    Route::prefix('admin')->middleware('auth:sanctum')->name('v1.authentication.admin.')->group(function (){
        // user routes
        Route::prefix('users')->name('user.')->group(function (){
            Route::get('all', [UserController::class, 'index'])->name('all');
            Route::post('store', [UserController::class, 'store'])->name('store');
            Route::put('update/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('delete/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
        // final admin routes
    });
});

Route::post('add/user/{user}/to-group/{group}', [\App\Http\Controllers\Api\v1\Admin\Group\GroupController::class, 'addUser']);
