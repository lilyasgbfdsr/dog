<?php

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

Route::namespace('Home')->group(function () {
    Route::get('/reserveIndex', 'ReserveController@index');
    Route::get('/reserveShow', 'ReserveController@show');
    Route::get('/reserveH5Show', 'ReserveController@h5Show');
    Route::post('/reserveCheck', 'ReserveController@check');
    Route::post('/reserveStore', 'ReserveController@store');
    Route::post('/reserveH5Store', 'ReserveController@h5Store');
    Route::get('/reserveLoding', 'ReserveController@loding');
    Route::any('/order/notify', 'ReserveController@notify'); // 回调路由
    Route::get('/order/index', 'ReserveController@orderIndex');
    Route::get('/orderH5Index', 'ReserveController@orderH5Index');
    Route::get('/order/list', 'ReserveController@orderList');
    Route::get('/getDate', 'ReserveController@getDate');

    Route::get('/test', 'ReserveController@test');
});