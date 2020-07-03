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
    Route::group([

        'middleware' => 'api',
        'prefix' => 'auth'

    ], function ($router) {

        Route::post('login', ['as' => 'login', 'uses' => 'Auth\LoginController@login']);
        Route::post('register', 'Auth\LoginController@register');
        Route::post('logout', 'Auth\LoginController@logout');
        Route::post('me', 'Auth\LoginController@me');
        //Route::post('refresh', 'Auth2\AuthController@refresh');
    });

    Route::apiResource('jugadores', 'GE\JugadoreController');
    Route::post('apuesta', 'GE\JugadoreController@apuesta');



