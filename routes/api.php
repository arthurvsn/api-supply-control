<?php

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

Route::group(['prefix' => 'password'], function () {
    Route::post('change', 'UserController@getTokenResetPassword');
    Route::post('reset/{token}', 'UserController@resetPassword');
});

/**
 * Rotas que precisam estar autenticadas
 */
Route::group(['middleware' => 'jwt.auth'], function () {
    
    Route::get('/ping', 'UserController@ping');

    Route::get('getAuthUser', 'UserController@getUserLogged');

    /**
     * Routes of users
     */
    Route::resource('user', 'UserController', ['except' => [
        'store', 'index'
    ]]);
    
    Route::group(['prefix' => 'user'], function () {
        Route::post('update/picture/{userID}', 'UserController@saveProfilePicture');
    });
    /**
     * Routes of cars
     */
    Route::resource('car', 'CarController');

    Route::group(['prefix' => 'car'], function () {
        Route::get('user/{userId}', 'CarController@getAllCarsByUser');
    });

    /**
     * Routes of supply
     */
    Route::resource('supply', 'SupplyController');
    
    Route::group(['prefix' => 'supply'], function () {
        Route::get('{dateStart}/{dateEnd}/{carID}', 'SupplyController@expensesMounth');
    });

});