<?php

use App\Http\Controllers\Api\Admin\Group\GroupController;
use App\Http\Controllers\Api\Admin\User\UserController;
use App\Http\Controllers\Api\Auth\AuthController;
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

Route::prefix('authentication')->name('authentication.')->group(function (){
    // login & register route
    Route::post('login-register', [AuthController::class, 'login'])->name('login');
    Route::post('check-otp', [AuthController::class, 'checkOtp'])->name('check.otp');
});

    // admin routes
    Route::prefix('admin')->middleware('auth:sanctum')->name('admin.')->group(function (){
        // user routes
        Route::prefix('users')->name('user.')->group(function (){
            Route::get('all', [UserController::class, 'index'])->name('all');
            Route::post('store', [UserController::class, 'store'])->name('store');
            Route::put('update/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('delete/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
        // group routes
        Route::prefix('groups')->name('group.')->group(function (){
            Route::get('all', [GroupController::class, 'index'])->name('all');
            Route::post('store', [GroupController::class, 'store'])->name('store');
            Route::put('update/{group}', [GroupController::class, 'update'])->name('update');
            Route::delete('delete/{group}', [GroupController::class, 'destroy'])->name('destroy');
            Route::post('add/user/{user}/to/group/{group}', [GroupController::class, 'addUser'])->name('add.user');
        });
        // final admin routes
    });

    Route::post('questions/store', [\App\Http\Controllers\Api\Admin\Poll\QuestionController::class, 'store']);
    Route::post('answer/user/{user}/question/{question}', [\App\Http\Controllers\Api\Admin\Poll\AnswerController::class, 'answer']);
    Route::get('answer/all', [\App\Http\Controllers\Api\Admin\Poll\AnswerController::class, 'index']);
