<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes(['verify' => true]);

Route::middleware('redirectIfNotAuth', 'checkRole')->prefix('admin')->group(function () {
        Route::get('/dashboard', 'App\Http\Controllers\HomeController@dashboard');
        Route::get('/admin_search', 'App\Http\Controllers\HomeController@adminSearch')->name('admin_search');

        Route::get('/statistics', 'App\Http\Controllers\StatisticsController@index')->name('statistics');
        Route::get('/statistics/user/{id}/downloads', 'App\Http\Controllers\StatisticsController@userDownload')->name('user_download');
        Route::get('/statistics/user/{id}/views', 'App\Http\Controllers\StatisticsController@userView')->name('user_view');
        Route::get('/statistics/content/{id}/downloads', 'App\Http\Controllers\StatisticsController@contentDownload')->name('content_download');
        Route::get('/statistics/content/{id}/views', 'App\Http\Controllers\StatisticsController@contentView')->name('content_view');

        Route::get('/contact_messages', 'App\Http\Controllers\HomeController@contactMessage');
        Route::get('/client_requests', 'App\Http\Controllers\HomeController@clientRequest')->name('client_requests');
        Route::patch('/accept_request/{id}', 'App\Http\Controllers\HomeController@acceptRequest')->name('accept_request');

        Route::get('/messages', 'App\Http\Controllers\MessageController@allMessages')->name('messages');
        Route::get('/new_message', 'App\Http\Controllers\MessageController@create')->name('new_message');
        Route::get('/new_message_with_user/{email}', 'App\Http\Controllers\MessageController@createWithUser')->name('new_message_with_user');
        Route::post('/send_message', 'App\Http\Controllers\MessageController@send');
        Route::get('/edit_message/{id}', 'App\Http\Controllers\MessageController@edit')->name('edit_message');
        Route::delete('/messages/{id}', 'App\Http\Controllers\MessageController@destroy');

        Route::get('/comments', 'App\Http\Controllers\CommentController@index')->name('comments');
        Route::delete('/comments/{id}', 'App\Http\Controllers\CommentController@destroy')->name('delete_comment');

        Route::resource('rules', 'App\Http\Controllers\RuleController');
        Route::resource('applications', 'App\Http\Controllers\ApplicationController');
        Route::resource('mitres', 'App\Http\Controllers\MitreController');
        Route::resource('usecases', 'App\Http\Controllers\UseCaseController');
        Route::resource('users', 'App\Http\Controllers\UserController');
        Route::resource('log_data', 'App\Http\Controllers\LogDataController');
        Route::resource('log_sources', 'App\Http\Controllers\LogSourceController');
        Route::resource('os_platforms', 'App\Http\Controllers\OsPlatformController');

        // accessible content managment
        Route::get('/accesses', 'App\Http\Controllers\UserController@accesses')->name('accesses');
        Route::get('/add-content/{id}', 'App\Http\Controllers\UserController@addContent')->name('add_content');
        Route::put('/accessible/{id}', 'App\Http\Controllers\UserController@accessible')->name('accessible');
        
        Route::get('/checkOnline', 'App\Http\Controllers\UserController@userOnlineStatus');
});


// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
