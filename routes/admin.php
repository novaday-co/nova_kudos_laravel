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

use App\Http\Controllers\Api\App\Coin\CoinController;
use App\Http\Controllers\Api\App\Company\AccountBalance\AdminBalanceController;
use App\Http\Controllers\Api\App\Company\CompanyController;
use App\Http\Controllers\Api\App\Company\GiftCard\GiftCardController;
use App\Http\Controllers\Api\App\Company\Medal\MedalController;
use App\Http\Controllers\Api\App\Company\Product\ProductController;
use App\Http\Controllers\Api\App\Company\User\UserController;
use Illuminate\Support\Facades\Route;

 Route::prefix('companies')->name('company.')->group(function () {
     Route::get('lists', [CompanyController::class, 'companyLists'])->name('list');
     Route::get('{company}/groups', [CompanyController::class, 'companyGroups'])->name('group');
  //   Route::get('{company}/users', [CompanyController::class, 'companyUsers'])->name('user');
     Route::get('{company}/owner', [CompanyController::class, '']);
     Route::post('store', [CompanyController::class, 'store'])->name('store');
     Route::post('{company_id}/update', [CompanyController::class, 'update'])->name('update');
     Route::delete('delete/{group}', [CompanyController::class, 'destroy'])->name('destroy');
     Route::post('{company}/owner/users/{user}', [CompanyController::class, 'addOwner'])->name('add.owner');
     Route::get('{company}/owner/get', [CompanyController::class, 'companyOwner'])->name('owner');
     Route::post('{company}/users/{user}', [CompanyController::class, 'addUser'])->name('add.user');
     Route::delete('{company}/users/{user}', [CompanyController::class, 'removeUser'])->name('remove.user');


     Route::prefix('{company_id}')->group(function (){
         Route::post('set/coin', [CoinController::class, 'updateValue'])->name('update.coin');
         Route::get('coin/system', [CoinController::class, 'getValueOfSystem'])->name('value.coin');
         Route::get('users/transactions', [AdminBalanceController::class, 'getTransactionUsers'])->name('transaction');
         Route::post('users/transactions/{transaction}/status', [AdminBalanceController::class, 'updateTransactionStatus'])->name('change.status');

         Route::get('market/products', [ProductController::class, 'index'])->name('product.index');
         Route::post('market/products', [ProductController::class, 'store'])->name('product.store');
         Route::post('market/products/{product}', [ProductController::class, 'update'])->name('product.update');
         Route::delete('market/products/{product}', [ProductController::class, 'update'])->name('product.destroy');

         Route::get('giftCards', [GiftCardController::class, 'index'])->name('giftCard.index');
         Route::post('giftCards', [GiftCardController::class, 'store'])->name('giftCard.store');
         Route::post('giftCards/{giftCard}', [GiftCardController::class, 'update'])->name('giftCard.update');
         Route::delete('giftCards/{giftCard}', [GiftCardController::class, 'destroy'])->name('giftCard.delete');

         // medal
         Route::get('medals', [MedalController::class, 'index'])->name('medal.index');
         Route::post('medals', [MedalController::class, 'store'])->name('medal.store');
         Route::post('medals/{medal_id}', [MedalController::class, 'update'])->name('medal.update');
         Route::get('search/medal', [MedalController::class, 'searchMedal'])->name('medal.search');

         Route::post('setting', [CompanyController::class, 'setSetting'])->name('setting');
     });



});

