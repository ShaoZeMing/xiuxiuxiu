<?php

namespace App\Providers;

use App\Repositories\MerchantAccountRepositoryEloquent;
use App\Repositories\MerchantRepositoryEloquent;
use App\Repositories\MerchantUserRepositoryEloquent;
use App\Repositories\OrderFeeRepositoryEloquent;
use App\Repositories\OrderRepositoryEloquent;
use App\Repositories\OrderTrackRepositoryEloquent;
use App\Repositories\PlatformAccountRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use App\Repositories\WorkerAccountRepositoryEloquent;
use App\Services\LuosidaoAccountService;
use App\Services\MerchantAccountService;
use App\Services\MerchantService;
use App\Services\OrderManagerService;
use App\Services\Orders\OrderBaseService;
use App\Services\OrderTrackService;
use App\Services\PlatformAccountService;
use App\Services\WorkerAccountService;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 添加自定义验证规则文件
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('OrderBaseService', function ($app) {

            $orderRepository = new OrderRepositoryEloquent($app);
            $workerAccountRepository = new WorkerAccountRepositoryEloquent($app);
            $merchantAccountRepository = new MerchantAccountRepositoryEloquent($app);
            $platformAccountRepository = new PlatformAccountRepositoryEloquent($app);
            $luosidaoAccountRepository = new LuosidaoAccountRepositoryEloquent($app);
            $userRepository = new UserRepositoryEloquent($app);
            $orderFeeRepository = new OrderFeeRepositoryEloquent($app);
            $merchantUserRepository = new MerchantUserRepositoryEloquent($app);
            $workerAccountService = new WorkerAccountService($workerAccountRepository);
            $platformAccountService = new PlatformAccountService($platformAccountRepository);
            $merchantAccountService = new MerchantAccountService($merchantAccountRepository);
            $luosidaoAccountService = new LuosidaoAccountService($luosidaoAccountRepository);
            return new OrderBaseService(
                $orderRepository,
                $merchantAccountService,
                $userRepository,
                $workerAccountService,
                $orderFeeRepository,
                $merchantUserRepository,
                $platformAccountService,
                $luosidaoAccountService
            );
        });

        $this->app->singleton('OrderManagerService', function ($app) {
            $orderRepository = new OrderRepositoryEloquent($app);
            $workerAccountRepository = new WorkerAccountRepositoryEloquent($app);
            $merchantAccountRepository = new MerchantAccountRepositoryEloquent($app);
            $platformAccountRepository = new PlatformAccountRepositoryEloquent($app);
            $userRepository = new UserRepositoryEloquent($app);
            $orderFeeRepository = new OrderFeeRepositoryEloquent($app);
            $merchantUserRepository = new MerchantUserRepositoryEloquent($app);
            $luosidaoAccountRepository = new LuosidaoAccountRepositoryEloquent($app);
            $workerAccountService = new WorkerAccountService($workerAccountRepository);
            $platformAccountService = new PlatformAccountService($platformAccountRepository);
            $merchantAccountService = new MerchantAccountService($merchantAccountRepository);
            $luosidaoAccountService = new LuosidaoAccountService($luosidaoAccountRepository);
            return new OrderManagerService(
                $orderRepository,
                $merchantAccountService,
                $userRepository,
                $workerAccountService,
                $orderFeeRepository,
                $merchantUserRepository,
                $platformAccountService,
                $luosidaoAccountService
            );
        });


        $this->app->singleton('MerchantService', function ($app) {
            $platformAccountRepository = new PlatformAccountRepositoryEloquent($app);
            $categoryRepository = new CategoryRepositoryEloquent($app);
            $merchantAccountRepository = new MerchantAccountRepositoryEloquent($app);
            $merchantRepository = new MerchantRepositoryEloquent($app);
            $merchantAccountService = new MerchantAccountService($merchantAccountRepository);
            $platformAccountService = new PlatformAccountService($platformAccountRepository);
            return new MerchantService(
                $merchantAccountRepository,
                $categoryRepository,
                $merchantRepository,
                $platformAccountService,
                $merchantAccountService
            );
        });

        $this->app->singleton('orderTrack', function ($app) {
            $orderRepository = new OrderRepositoryEloquent($app);
            $orderTrackRepository = new OrderTrackRepositoryEloquent($app);
            return new OrderTrackService(
                $orderRepository,
                $orderTrackRepository
            );
        });
    }
}
