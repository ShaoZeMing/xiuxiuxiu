<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {

        //前台网站接口
        Route::middleware('web')
             ->namespace($this->namespace.'\Web')
             ->group(base_path('routes/web.php'));

        //后台网站接口
        Route::prefix('admin')
            ->middleware('web')
             ->namespace($this->namespace.'\Admin')
             ->group(base_path('routes/admin.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        //app 接口
        Route::prefix('app')
             ->middleware('api')
             ->namespace($this->namespace.'\App')
             ->group(base_path('routes/api.php'));


        //公用接口
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace.'\Api')
             ->group(base_path('routes/api.php'));


        //微信接口
        Route::prefix('wx')
             ->middleware('api')
             ->namespace($this->namespace.'\WX')
             ->group(base_path('routes/wx.php'));
    }
}
