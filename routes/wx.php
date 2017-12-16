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



Route::group([
//    'middleware' => [
//        'web',
//    ],
], function ($api) {
    Route::get('create/menu', 'AuthController@createMenu');//微信自动触发事件
    Route::any('event', 'AuthController@event');//微信自动触发事件
    Route::any('auth/{type}', 'AuthController@auth');//微信授权成功后跳转路由

    Route::get('user/order/index', 'UserOrderController@index');//授权事件

});
