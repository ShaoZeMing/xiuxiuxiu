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

/**
 * 全局使用接口
 */
Route::group([
//    'middleware' => [
//        'web',
//    ],
], function ($api) {
    Route::get('area', 'AreaController@area');//获取地理位置
});
