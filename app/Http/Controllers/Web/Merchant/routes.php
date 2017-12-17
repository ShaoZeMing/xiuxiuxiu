<?php

use Illuminate\Routing\Router;

Merchant::registerAuthRoutes();

Route::group([
    'prefix'        => config('merchant.route.prefix'),
    'namespace'     => config('merchant.route.namespace'),
    'middleware'    => config('merchant.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

});
