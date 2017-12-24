<?php

namespace App\Providers;

use App\Services\Vendor\Amap;
use App\Services\Vendor\Sequence;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //验证签名
        $this->app->singleton('sequence', function ($app) {
            return new Sequence(1);
        });


        //阿里地图接口
        $this->app->singleton('amap', function ($app) {
            return new Amap($app);
        });
    }
}
