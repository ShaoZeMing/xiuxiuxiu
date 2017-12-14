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
    //公众号授权及操作路由
    Route::any('user/order/index', 'UserController@userOrderAuthCallback');//授权事件
    Route::any('user/order/auth', 'UserController@userOrderAuthCallback');//授权事件

});
