<?php

namespace App\Providers;

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
        //
        $this->app->singleton('sequence', function ($app) {
        return new Sequence(1);
    });
    }
}
