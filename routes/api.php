<?php

use Illuminate\Http\Request;

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

/**
 *  login route
 */
Route::post('/login', 'LoginController@login')->name('login');

/***
 * To store user information in to database
 */
Route::post('/user', 'UserController@store')->name('post');

/***
 * To get all existing users info from database
 * only loggedin users can access this resource
 */
Route::middleware('auth:api')->get('/user', 'UserController@index')->name('index');

/***
 * To update existing user info in to database
 * only loggedin users can access this resource
 */
Route::middleware('auth:api')->put('/user/update/{id}', 'UserController@update')->name('update');
/***
 * To delete existing user info in to database
 * only loggedin users can access this resource
 */
Route::middleware('auth:api')->delete('/user/delete/{id}', 'UserController@destroy')->name('destroy');
/***
 * upload the users information through csv file.it will be stored in app/public/csv folder
 * only loggedin users can access this resource
 */
Route::middleware('auth:api')->post('/upload', 'UserController@uploadfile')->name('uploadfile');
