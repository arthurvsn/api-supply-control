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

Route::post('teste', 'HomeController@store');
Route::get('/user', 'UserController@index');
Route::post('/register', 'UserController@store');
Route::post('/login', 'UserController@login');

Route::get('type-fuel', 'SupplyController@getTypeFuel');
/**
 * Routes of change password
 */
Route::post('password/change', 'UserController@getTokenResetPassword');
Route::post('password/reset/{token}', 'UserController@resetPassword');

/**
 * Rotas que precisam estar autenticadas
 */
Route::group(['middleware' => 'jwt.auth'], function () {
    
    Route::get('/ping', 'UserController@ping');

    Route::get('getAuthUser', 'UserController@getUserLogged');
    
    Route::post('user/update/picture/{userID}', 'UserController@saveProfilePicture');

    /**
     * Routes of users
     */
    Route::resource('user', 'UserController', ['except' => [
        'store', 'index'
    ]]); 

    /**
     * Routes of cars
     */
    Route::resource('car', 'CarController');
    Route::get('car/user/{userId}', 'CarController@getAllCarsByUser');

    /**
     * Routes of supply
     */
    Route::resource('supply', 'SupplyController');
    
    Route::get('supply/{dateStart}/{dateEnd}/{carID}', 'SupplyController@expensesMounth');

});