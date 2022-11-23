<?php

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

Route::prefix('v1')->name('authentication.')->group(function (){
    // login & register routes
    Route::post('register', [\App\Http\Controllers\Api\v1\Auth\LoginRegisterController::class, 'register'])->name('register');
    Route::post('login', [\App\Http\Controllers\Api\v1\Auth\LoginRegisterController::class, 'login'])->name('login');
});
