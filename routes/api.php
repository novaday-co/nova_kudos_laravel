<?php

use App\Http\Controllers\Api\Admin\Event\EventController;
use App\Http\Controllers\Api\Admin\Group\GroupController;
use App\Http\Controllers\Api\Admin\Poll\AnswerController;
use App\Http\Controllers\Api\Admin\Poll\QuestionController;
use App\Http\Controllers\Api\Admin\Poll\VoteController;
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
                Route::get('all', [EventController::class, 'index'])->name('all');
                Route::post('users/{user}/store', [EventController::class, 'store'])->name('store');
                Route::put('update/{event}', [EventController::class, 'update'])->name('update');
                Route::delete('delete/{event}', [EventController::class, 'destroy'])->name('destroy');
                Route::post('{event}/type/users/{user}', [EventController::class, 'userType'])->name('user.type');
                Route::post('{event}/type/groups/{group}', [EventController::class, 'groupType'])->name('group.type');
                Route::post('{event}/join/users/{user}', [EventController::class, 'participateUser']);
                Route::post('{event}/join/groups/{group}', [EventController::class, 'participateGroup']);
            });
        });
        // final admin routes
    });
