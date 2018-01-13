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

Route::get('/', function () {
    return view('welcome');
});



/**********这是web全局使用路由************/

Route::group([
    'namespace'     => config('merchant.route.namespace'),
], function ($api) {
    Route::get('/', function () {
        return view('welcome');
    });
});


/**********这是企业权限控制路由************/
use Illuminate\Routing\Router;



Merchant::registerAuthRoutes();
Route::group([
    'prefix'        => config('merchant.route.prefix'),
    'namespace'     => config('merchant.route.namespace'),
    'middleware'    => config('merchant.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('auth/login', 'AuthController@getLogin');
    $router->post('auth/login', 'AuthController@postLogin');

    $router->resource('cats', CategorieController::class);
    $router->get('api/cat','CategorieController@apiCats');
    $router->get('api/cat/malfunctions','CategorieController@apiMalfunctions');
    $router->get('api/cat/products','CategorieController@apiProducts');



    $router->resource('brands', BrandController::class);
    $router->get('api/brand/cats','BrandController@apiCats');



    $router->resource('products', ProductController::class);
    $router->get('api/product/malfunctions','ProductController@apiMalfunctions');


    $router->resource('malfunctions', MalfunctionController::class);
    $router->get('api/malfunction/resolvents','MalfunctionController@apiResolvents');


    $router->resource('service-types', ServiceTypeController::class);


    $router->resource('merchants', MerchantController::class);
    $router->post('api/merchants/{id}/cats', 'MerchantController@cats');
    $router->post('api/merchants/{id}/brands', 'MerchantController@brands');

    $router->resource('test', ExampleController::class);

});

Route::group([
    'prefix'        => config('merchant.route.prefix'),
    'namespace'     => config('merchant.route.namespace'),
    'middleware'    => ['web'],
], function (Router $router) {
    $router->get('auth/forget', 'AuthController@getForget');
    $router->get('auth/register', 'AuthController@getRegister');
    $router->post('auth/register', 'AuthController@postRegister');
    $router->post('auth/forget', 'AuthController@postForget');

});




