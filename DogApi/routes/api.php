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

Route::group(['prefix'=>'admin', 'namespace'=>'Admin'], function () {
    Route::post('/login', 'LoginController@login');
    Route::get('/getCheckVerifyCode', 'LoginController@getCheckVerifyCode');
    Route::post('/info', 'LoginController@info');

    Route::post('/index', 'IndexController@index');
    Route::post('/reserveList', 'ReserveController@index');
    Route::post('/reserveDelete', 'ReserveController@delete');
    Route::post('/reserveCheck', 'ReserveController@check');
    Route::post('/reserveUpdate', 'ReserveController@update');
    Route::post('/reserveSign', 'ReserveController@sign');

    Route::post('/infoList', 'ReserveController@infoList');
    Route::post('/infoStore', 'ReserveController@infoStore');
    Route::post('/infoDelete', 'ReserveController@infoDelete');

    // 配置相关模块
    Route::get('/dateTime', 'DateTimesController@index');
    Route::post('/dateTime', 'DateTimesController@store');
    Route::post('/editDateTime', 'DateTimesController@editDateTime');
    Route::post('/deleteDateTime', 'DateTimesController@deleteDateTime');

    Route::get('/tip', 'TipsController@index');
    Route::post('/tip', 'TipsController@store');
    Route::post('/deleteTip', 'TipsController@deleteTip');

    Route::get('/configPrice', 'ConfigPriceController@index');
    Route::post('/configPrice', 'ConfigPriceController@store');
});
