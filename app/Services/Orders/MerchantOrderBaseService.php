<?php
namespace App\Services\Orders;

use App\Entities\Merchant;
use App\Entities\MerchantBill;
use App\Entities\Order;
use App\Repositories\CustomerRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\MerchantRepositoryEloquent;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Ixudra\Curl\Facades\Curl;
use Shaozeming\LumenPostgis\Geometries\GeomPoint;

/**
 *  OrderService.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: OrderService.php 2017-03-21 下午4:57 $
 */
class MerchantOrderBaseService implements OrderServiceInterface
{


    public $orderRepository;
    public $merchantRepository;


    public static $service;

    protected function __construct(OrderRepository $orderRepository,MerchantRepositoryEloquent $merchantRepository) {
        $this->orderRepository = $orderRepository;
        $this->merchantRepository = $merchantRepository;
        $this->retryTimes = config('app.retry_times');
        $this->retrySleep = config('app.retry_sleep');
    }



    public static function getClassName()
    {
        return "MerchantOrderBaseService";
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $orderRepository
     * @return mixed
     * 获取单例
     */
    public static function getInstanceService($orderRepository,$merchantRepository)
    {
        $service = static::getClassName();
        if (!self::$service) {
            $className = 'App\\Services\\Orders\\' . $service;
            self::$service = new $className($orderRepository,$merchantRepository);
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

        $fullAddress = $data['province'] . $data['city'] . $data['district'] . $data['address'];
        $geo = app('amap')->getLocation($fullAddress, $data['province'] . $data['city'] . $data['district']);
        $location = $geo->location;
        list($lng, $lat) = explode(',', $location);
        $data['geom'] = new GeomPoint($lat,$lng);

        //创建商家客户
        $userAttr = [
            'customer_mobile' => $data['connect_mobile'],
        ];
        $userData = [
            'customer_name' => $data['connect_name'],
            'customer_province' => $data['province'],
            'customer_city' => $data['city'],
            'customer_district' => $data['district'],
            'customer_address' => $data['address'],
            'customer_full_address' => $fullAddress,
            'customer_lng' => $lng,
            'customer_lat' => $lat,
            'customer_geom' => $data['geom'],
        ];


        $merchantId = getMerchantId();
        $merchant = Merchant::find($merchantId);
        $customer = $merchant->customers()->updateOrCreate($userAttr,$userData);
        $data['full_address'] = $fullAddress;
        $data['lng'] = $lng;
        $data['lat'] = $lat;
        Log::info('查看创建order信息', [$data]);
        $order = $merchant->orders()->create($data);
        return $order;
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $id
     * @param $date
     * @return mixed
     * 更新工单
     */
    public function update($id, $data){
        $fullAddress = $data['province'] . $data['city'] . $data['district'] . $data['address'];
        $geo = app('amap')->getLocation($fullAddress, $data['province'] . $data['city'] . $data['district']);
        $location = $geo->location;
        list($lng, $lat) = explode(',', $location);
        $data['geom'] = new GeomPoint($lat,$lng);

        //创建商家客户
        $userAttr = [
            'customer_mobile' => $data['connect_mobile'],
        ];
        $userData = [
            'customer_name' => $data['connect_name'],
            'customer_province' => $data['province'],
            'customer_city' => $data['city'],
            'customer_district' => $data['district'],
            'customer_address' => $data['address'],
            'customer_full_address' => $fullAddress,
            'customer_lng' => $lng,
            'customer_lat' => $lat,
            'customer_geom' => $data['geom'],
        ];


        $merchantId = getMerchantId();
        $merchant = Merchant::find($merchantId);
        $customer = $merchant->customers()->updateOrCreate($userAttr,$userData);
        $data['full_address'] = $fullAddress;
        $data['lng'] = $lng;
        $data['lat'] = $lat;
        Log::info('查看创建order信息', [$data]);
        $order = $merchant->orders()->where('orders.id',$id)->update($data);
        return $order;
    }


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
