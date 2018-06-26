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

Route::get('/', 'HomeController@index');

Route::get('/user', 'UserController@index');
Route::post('/register', 'UserController@store');
Route::post('/login', 'UserController@login');

Route::group(['middleware' => 'jwt.auth'], function () {
    
    Route::get('/ping', 'UserController@ping');

    Route::get('getAuthUser', 'UserController@getUserLogged');
    
    //routes of users
    Route::resource('user', 'UserController', ['except' => [
        'store', 'index'
    ]]); 

    //Routes of cars
    Route::resource('car', 'CarController');
    Route::get('car/user/{userId}', 'CarController@getAllCarsByUser');

    //Routes of supply
    Route::resource('supply', 'SupplyController');
    Route::get('supply/{dateStart}/{dateEnd}/{carID}', 'SupplyController@expensesMounth');
});