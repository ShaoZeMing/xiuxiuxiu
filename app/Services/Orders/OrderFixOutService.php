<?php
namespace App\Services\Orders;

use App\Entities\Order;
use App\Services\OrderBillingService;
use Illuminate\Support\Facades\Log;

/**
 *  OrderService.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: OrderService.php 2017-03-21 下午4:57 $
 */
class OrderFixOutService extends OrderBaseOutService
{
    /**
     * 商家资金账户
     *
     * @var \App\Repositories\MerchantAccountRepository
     */
    public $merchantAccountService;
    /**
     * 工单
     *
     * @var \App\Repositories\OrderRepositoryEloquent
     */
    public $orderRepository;
    /**
     * 用户
     *
     * @var \App\Repositories\UserRepository
     */
    public $userRepository;
    /**
     * 工单费用
     *
     * @var \App\Repositories\OrderFeeRepository
     */
    public $orderFeeRepository;
    /**
     * @var \App\Repositories\WorkerAccountRepository
     */
    public $workerAccountService;

    /**
     * @var \App\Repositories\PlatformAccountService
     */
    public $platformAccountService;
    public $retryTimes = 3;
    public $retrySleep = 0;
    public static $fixOutService;

    public static function getClassName()
    {
        return "OrderFixOutService";
    }

    /**
     * 创建工单
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data)
    {
        $data = $this->creating($data);
        Log::info('查看创建order信息', [$data]);
        //父类写入用户，创建order
        $order = parent::create($data);

        return $order;
    }

    /**
     * 更新工单
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function update($id, $data)
    {
        $data = $this->updating($data);
        $fullAddress = $data['province'] . $data['city'] . $data['district'] . $data['address'];
        $geo = app('amap')->getLocation($fullAddress, $data['province'] . $data['city'] . $data['district']);
        $location = $geo->location;
        list($lng, $lat) = explode(',', $location);

        $data['full_address'] = $fullAddress;
        $data['user_lng'] = $lng;
        $data['user_lat'] = $lat;

        Log::info('查看创建order信息', $data);
        $order = $this->orderRepository->update($data, $id);

        $this->updateUserInfo($order->user_id, $data);

        return $order;
    }

    /**
     * 监听工单创建前的事件。
     *
     * @param array $data
     *
     * @return array
     * @throws \App\Exceptions\OrderException
     * @internal param \App\Entities\Order|array $order
     *
     */
    public function updating(array $data)
    {
        Log::info($data, [
            'f' => __METHOD__,
            'msg' => '更新数据updating start',
        ]);
        $order = $this->orderRepository->makeModel()->newInstance($data);
        /** @var \App\Entities\Order $order */
        // 保外 维修单
        $order->urgent_fee = 0;   //保外没有加急费
        $order->inspect_fee = 0; //保外没有检测费
        $data['fee'] = isset($data['fee']) ? $data['fee'] : $order->fix_fee;
        $order->fix_fee = yuanToFen($data['fee']);
        $order->fix_fee = OrderBillingService::outPeriodPrice($order->fix_fee);
        //测试白名单
        $merchants=config('white.merchant_out', []);
        if (in_array($order->merchant->mobile, $merchants)) {
            $order->fix_fee = 1;
        }
        $order->price = $order->fix_fee;
        $order->install_fee = 0; //安装费还原
        $order->is_inspect = 0;
        $order->is_fix = 1;
        $order->urgent_fee = yuanToFen($order->urgent_fee);
        $order->merchant_brokerage = 0;
        $order->price += $order->urgent_fee;
        $order->freeze_fee = 0;  //保外没有预冻结费


        Log::info($order->toArray(), [
            'f' => __METHOD__,
            'msg' => '更新数据updating end',
        ]);

        return $order->toArray();
    }

    /**
     * 监听工单创建前的事件。
     *
     * @param  array $data
     *
     * @return array
     * @throws \App\Exceptions\OrderException
     */
    public function creating(array $data)
    {
        Log::info($data, [
            'f' => __METHOD__,
            'msg' => '更新数据creating start',
        ]);

        $order = $this->orderRepository->makeModel()->newInstance($data);
        /** @var Order $order */
        // 保内 维修单
        $order->urgent_fee = 0;   //保外没有加急费
        $order->inspect_fee = 0; //保外没有检测费
        $order->install_cnt = 1; //保外没有检测费
        $data['fee'] = isset($data['fee']) ? $data['fee'] : $order->fix_fee;
        $order->fix_fee = yuanToFen($data['fee']);
        $order->fix_fee = OrderBillingService::outPeriodPrice($order->fix_fee);

        //测试白名单
        $merchants=config('white.merchant_out', []);
        if (in_array($order->merchant->mobile, $merchants)) {
            $order->fix_fee =1;
        }
        $order->price = $order->fix_fee;
        $order->install_fee = 0; //安装费还原
        $order->is_inspect = 0;
        $order->is_fix = 1;
        $order->urgent_fee = yuanToFen($order->urgent_fee);
        $order->merchant_brokerage = 0;
        $order->order_no = app('sequence')->generateOrderNo();
        $order->freeze_fee = 0;  //保外没有预冻结费


        Log::info($order->toArray(), [
            'f' => __METHOD__,
            'msg' => '查看创建order信息 end',
        ]);
        return $order->toArray();
    }
}
