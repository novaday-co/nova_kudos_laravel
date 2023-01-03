<?php

use App\Http\Controllers\Api\App\Company\AccountBalance\BalanceController;
use App\Http\Controllers\Api\App\Company\AccountInfo\AccountInfoController;
use App\Http\Controllers\Api\App\Company\Exchange\ExchangeController;
use App\Http\Controllers\Api\App\Company\GiftCard\UserGiftCardController;
use App\Http\Controllers\Api\App\Company\Medal\MedalController;
use App\Http\Controllers\Api\App\Company\User\UserController;
use App\Http\Controllers\Api\App\Event\EventController;
use App\Http\Controllers\Api\App\Group\GroupController;
use App\Http\Controllers\Api\App\Home\HomeController;
use App\Http\Controllers\Api\App\Poll\AnswerController;
use App\Http\Controllers\Api\App\Poll\QuestionController;
use App\Http\Controllers\Api\App\Poll\VoteController;
use App\Http\Controllers\Api\App\Profile\ProfileController;
use App\Http\Controllers\Api\Auth\AuthController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix('authentication')->name('authentication.')->group(function (){
    // login & register route
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('check-otp', [AuthController::class, 'checkOtp'])->name('check.otp');
    Route::post('resend-otp', [AuthController::class, 'resendOtp'])->name('resend.otp');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
});

// user routes
Route::prefix('users')->middleware('auth:sanctum')->name('user.')->group(function (){
    Route::post('change/mobile', [ProfileController::class, 'updateMobile'])->name('update.mobile');
    Route::post('verify/mobile', [ProfileController::class, 'verifyMobile'])->name('verify.mobile');
    Route::get('account/detail', [AccountInfoController::class, 'defaultCompany'])->name('default.company');
    // companies
    Route::prefix('companies/{company_id}')->middleware('auth:sanctum')->name('companies.')->group(function (){
        Route::post('change-avatar', [ProfileController::class, 'updateProfile'])->name('update');
        Route::post('exchange/coin', [ExchangeController::class, 'exchangeCoin'])->name('exchange.coin');
        Route::post('exchange/currency', [ExchangeController::class, 'exchangeCurrency'])->name('exchange.currency');
        Route::post('withdrawal', [BalanceController::class, 'withdrawalCurrency'])->name('user.withdrawal');
        Route::get('transactions', [BalanceController::class, 'getUserTransaction'])->name('user.transaction');
        Route::post('send/giftCard', [UserGiftCardController::class, 'sendGiftCard'])->name('gift.send');
        Route::get('giftCards', [UserGiftCardController::class, 'index'])->name('gift.index');
        Route::get('members', [UserController::class, 'getAllUser'])->name('user.index');
        Route::get('search/user', [UserGiftCardController::class, 'searchUser'])->name('search.user');
    });
});
// group routes
Route::prefix('groups')->name('group.')->group(function (){
    Route::post('companies/{company}', [GroupController::class, 'store'])->name('store');
    Route::put('{group}/companies/{company}', [GroupController::class, 'update'])->name('update');
    Route::delete('delete/{group}', [GroupController::class, 'destroy'])->name('destroy');
    Route::post('{group}/companies/{company}/users/{user}', [GroupController::class, 'addUser'])->name('add.user');
    Route::get('{group}/companies/{company}', [GroupController::class, 'groupUsers'])->name('user.list');
});
// poll routes
Route::prefix('polls')->name('poll.')->group(function (){
    //question routes
    Route::prefix('questions')->name('question.')->group(function (){
        Route::get('all', [QuestionController::class, 'index'])->name('all');
        Route::post('store', [QuestionController::class, 'store'])->name('store');
        Route::put('update/{question}', [QuestionController::class, 'update'])->name('update');
        Route::delete('delete/{question}', [QuestionController::class, 'destroy'])->name('destroy');
        Route::post('type/{question}/for/users/{user}', [QuestionController::class, 'userType'])->name('type.user');
        Route::post('type/{question}/for/group/{group}', [QuestionController::class, 'groupType'])->name('type.group');
    });
    // answer routes
    Route::prefix('answers')->name('answer.')->group(function (){
        Route::get('all', [AnswerController::class, 'index'])->name('all');
        Route::post('users/{user}/question/{question}', [AnswerController::class, 'answer'])->name('store');
    });
    // vote routes
    Route::prefix('votes')->name('vote')->group(function (){
        Route::post('users/{user}/answers/{answer}/questions/{question}', [VoteController::class, 'voteQuestion'])->name('store');
    });
    // the end of poll routes
    // events routes
    Route::prefix('events')->name('event.')->group(function (){
        Route::post('companies/{company}', [EventController::class, 'store'])->name('store');
        Route::put('update/{event}', [EventController::class, 'update'])->name('update');
        Route::delete('delete/{event}', [EventController::class, 'destroy'])->name('destroy');
        Route::post('{event}/type/users/{user}', [EventController::class, 'userType'])->name('user.type');
        Route::post('{event}/type/groups/{group}', [EventController::class, 'groupType'])->name('group.type');
        Route::post('{event}/join/users/{user}', [EventController::class, 'participateUser']);
        Route::post('{event}/join/groups/{group}', [EventController::class, 'participateGroup']);
    });
});
// final app routes
