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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['cors', 'json.response']], function () {
    Route::post('/login',  [App\Http\Controllers\Auth\ApiAuthController::class, 'login'])->name('login.api');
    Route::post('/register', [App\Http\Controllers\Auth\ApiAuthController::class, 'register'])->name('register.api');
    Route::get('verification/{token}', [App\Http\Controllers\Auth\ApiAuthController::class, 'verifyAccount']);
});

Route::middleware('auth:api')->group(function () {
    // our routes to be protected will go in here
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);
    Route::post('/logout', [App\Http\Controllers\Auth\ApiAuthController::class, 'logout'])->name('logout.api');


    Route::get('/notifications', [\App\Http\Controllers\UserPanelController::class, 'notifications']);
    Route::post('/seen', [\App\Http\Controllers\UserPanelController::class, 'seen']);
    Route::get('/new_notifications', [\App\Http\Controllers\UserPanelController::class, 'hasUnreadNotif']);


    Route::get('/rules/{id}', [\App\Http\Controllers\RuleController::class, 'show']);
    Route::get('/applications/{id}', [\App\Http\Controllers\ApplicationController::class, 'show']);

    Route::post('/comments', [\App\Http\Controllers\CommentController::class, 'insert']);
    Route::get('/comments/{content_id}', [\App\Http\Controllers\CommentController::class, 'comments']);

    Route::post('/fav', [\App\Http\Controllers\FavouriteController::class, 'fav']);
    Route::delete('/fav/{id}', [\App\Http\Controllers\FavouriteController::class, 'unfav']);
    Route::get('/fav', [\App\Http\Controllers\FavouriteController::class, 'favs']);
    Route::post('/like', [\App\Http\Controllers\LikeController::class, 'likeDislike']);
    Route::post('/vote', [\App\Http\Controllers\VoteController::class, 'vote']);
    Route::post('/download', [\App\Http\Controllers\DownloadController::class, 'download']);

    // fetch data in home
    Route::get('/top',  [\App\Http\Controllers\HomeController::class, 'topContent']);
    Route::get('/recent',  [\App\Http\Controllers\HomeController::class, 'recentContent']);
    Route::get('/search', [\App\Http\Controllers\HomeController::class, 'search']);
    Route::get('/filter', [\App\Http\Controllers\HomeController::class, 'filter']);

    Route::prefix('user_panel')->group(function () {
        Route::post('/settings', [\App\Http\Controllers\UserPanelController::class, 'settings']);
        Route::post('/request_client', [\App\Http\Controllers\UserPanelController::class, 'submitRequest']);
        Route::get('/accessible', [\App\Http\Controllers\UserPanelController::class, 'accessible']);
        Route::get('/history', [\App\Http\Controllers\UserPanelController::class, 'history']);
        Route::get('/fix_info', [\App\Http\Controllers\UserPanelController::class, 'fixInfo']);
    });
});

// contact us form
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'submit']);


// mitres
Route::get('/tactics', [App\Http\Controllers\MitreController::class, 'getTactics']);
Route::get('/techniques', [App\Http\Controllers\MitreController::class, 'getTechniques']);
Route::get('/sub_techniques', [App\Http\Controllers\MitreController::class, 'getSubTechniques']);

// use cases
Route::get('/level1', [App\Http\Controllers\UseCaseController::class, 'getLevel1']);
Route::get('/level2', [App\Http\Controllers\UseCaseController::class, 'getLevel2']);
Route::get('/smcat', [App\Http\Controllers\UseCaseController::class, 'getSmcat']);

// log sources
Route::get('/logsource', [App\Http\Controllers\LogSourceController::class, 'getLogSources']);

// log data
Route::get('/logdata', [App\Http\Controllers\LogDataController::class, 'getLogData']);

// os/platform
Route::get('/osplatform', [App\Http\Controllers\OsPlatformController::class, 'getOsPlatforms']);

// tags
Route::post('/tags', [App\Http\Controllers\RuleController::class, 'tags']);

Route::group([
    'namespace' => 'Auth',
    'middleware' => 'api',
    'prefix' => 'password'
], function () {
    Route::post('/create-token', [App\Http\Controllers\Auth\ApiAuthController::class, 'createTokenResetPassword']);
    Route::get('find/{token}', [App\Http\Controllers\Auth\ApiAuthController::class, 'findTokenResetPassword']);
    Route::post('/reset', [App\Http\Controllers\Auth\ApiAuthController::class, 'resetPassword']);
});
