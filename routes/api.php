<?php

use App\Http\Controllers\Api\App\Company\CompanyController;
use App\Http\Controllers\Api\App\GiftCard\SendGiftCardController;
use App\Http\Controllers\Api\App\Group\GroupController;
use App\Http\Controllers\Api\App\Home\HomeController;
use App\Http\Controllers\Api\App\Poll\AnswerController;
use App\Http\Controllers\Api\App\Poll\QuestionController;
use App\Http\Controllers\Api\App\Poll\VoteController;
use App\Http\Controllers\Api\App\Profile\ProfileController;
use App\Http\Controllers\Api\App\User\UserController;
use App\Http\Controllers\Api\App\Event\EventController;
use App\Http\Controllers\Api\App\Medal\MedalController;
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
        Route::prefix('users')->name('user.')->group(function (){
            Route::get('all', [UserController::class, 'index'])->name('all');
            Route::post('store', [UserController::class, 'store'])->name('store');
            Route::put('update/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('delete/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
        // profile routes
        Route::prefix('profiles')->middleware('auth:sanctum')->name('profile.')->group(function (){
           Route::post('companies/{company_id}/users/avatar', [ProfileController::class, 'updateProfile'])->name('update.avatar');
           Route::post('users/update/mobile', [ProfileController::class, 'updateMobile'])->name('update.mobile');
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




    // Route::post('events/users/{user}', [EventController::class, 'store']);
    // Route::post('users/{user}/groups/{group}', [GroupController::class, 'addUser']);
    Route::post('medals/store', [MedalController::class, 'store']);
    // Route::put('update/medals/{medal}', [MedalController::class, 'update']);
        Route::post('medals/{medal}/questions/{question}', [MedalController::class, 'medalQuestion']);
        Route::post('medals/{medal}/users/{user}/questions/{question}', [MedalController::class, 'medalUser']);


        Route::get('questions/users/{user}', [HomeController::class, 'questions']);
        Route::get('answers/questions/{question}', [HomeController::class, 'answerQuestions']);
        Route::post('questions/{question}/users/{user}', [QuestionController::class, 'userType'])->name('type.user');
        Route::get('questions/{question}/votes', [HomeController::class, 'countOfVotes']);
        Route::post('users/{from_id}/users/{to_id}/gifts/{gift_id}', [SendGiftCardController::class, 'sendTo']);

        // profile
        Route::get('users/{user}/profile', [ProfileController::class, 'show']);
        Route::put('users/{user}/profile', [ProfileController::class, 'update']);

