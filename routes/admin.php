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

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();
Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('cats', CategorieController::class);
    $router->resource('brands', BrandController::class);
    $router->resource('products', ProductController::class);
    $router->resource('malfunctions', MalfunctionController::class);
    $router->resource('service-types', ServiceTypeController::class);
    $router->resource('test', ExampleController::class);

});