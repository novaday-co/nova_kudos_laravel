<?php

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Super Admin API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "superAdmin" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\Api\App\Company\CompanyController;
use Illuminate\Support\Facades\Route;

Route::prefix('companies')->name('company.')->group(function (){
    Route::get('all', [CompanyController::class, 'index'])->name('all');
    Route::post('store', [CompanyController::class, 'store'])->name('store');
    Route::put('update/{group}', [CompanyController::class, 'update'])->name('update');
    Route::delete('delete/{group}', [CompanyController::class, 'destroy'])->name('destroy');
    // Route::post('users/{user}/groups/{group}', [GroupController::class, 'addUser'])->name('add.user');
});
