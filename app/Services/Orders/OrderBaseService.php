<?php
namespace App\Services\Orders;

use App\Entities\MerchantBill;
use App\Entities\Order;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Ixudra\Curl\Facades\Curl;


/**
 * Class OrderBaseService
 * @package App\Services\Orders
 */
class OrderBaseService implements OrderServiceInterface
{


    public $orderRepository;
    public static $service;

    protected function __construct(OrderRepository $orderRepository) {
        $this->orderRepository = $orderRepository;
        $this->retryTimes = config('app.retry_times');
        $this->retrySleep = config('app.retry_sleep');
    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return string
     */
    public static function getClassName()
    {
        return "OrderBaseService";
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $orderRepository
     * @return mixed
     * 获取单例
     */
    public static function getInstanceService($orderRepository)
    {
        $service = static::getClassName();
        if (!self::$service) {
            $className = 'App\\Services\\Orders\\' . $service;
            self::$service = new $className($orderRepository);
        }
        return self::$service;
    }


    /**
     * 创建工单
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param array $data
     * @return mixed
     */
    public function create(array $data){

    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $id
     * @param $date
     * @return mixed
     * 更新工单
     */
    public function update($id, $date){}


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $id
     * @return mixed
     * 确认工单
     */
    public function confirm($id){}


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $id
     * @return mixed
     * 取消工单
     */
    public function cancel($id){}


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param array $data
     * @return mixed
     * 更新前动作
     */
    public function updating(array $data){}


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param array $data
     * @return mixed
     * 创建前动作
     */
    public function creating(array $data){}


}
