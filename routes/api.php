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

Route::middleware('auth:api')->get('/restricted', function (Request $request) {
    return $request->user();
});

Route::resource('/ads', 'AdsController');
Route::put('/ads/{ad}/extend', 'AdsController@extendAd')->middleware('auth:api');
Route::post('/ads/{ad}/grades', 'AdsController@grades')->middleware('auth:api');

Route::post('/register', 'RegisterController@store');
Route::post('/refresh-token', 'RegisterController@refreshToken');
