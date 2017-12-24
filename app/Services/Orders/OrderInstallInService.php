<?php
namespace App\Services\Orders;

use App\Entities\Order;
use App\Exceptions\OrderException;
use App\Services\OrderBillingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;

/**
 *  OrderService.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: OrderService.php 2017-03-21 下午4:57 $
 */
class OrderInstallInService extends OrderBaseInService
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

    public static $installInService;

    public static function getClassName()
    {
        return "OrderInstallInService";
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
        // 保内 // 安装单
        $order->install_fee = yuanToFen($order->install_fee);
        $order->price = $order->install_cnt * $order->install_fee;
        $order->is_inspect = 0;
        $order->fix_fee = 0;  //维修费还原
        $order->is_fix = 0;
        $order->inspect_fee = 0; //检测费还原
//        $order->merchant_brokerage = OrderBillingService::merchantBrokerage($order->price, intval($order->order_type), $order->merchant_id);

        $order->merchant_brokerage = 0;
        $order->urgent_fee = yuanToFen($order->urgent_fee);
        $order->price += $order->urgent_fee;
        $order->freeze_fee = app('MerchantService')->getCatPrice($order->middle_cat_id,$order->install_cnt); //预冻结费用
        $order->freeze_fee += $order->urgent_fee;
        $order->freeze_fee = ($order->price < $order->freeze_fee) ? $order->freeze_fee : $order->price;


        $merchant = $order->merchant()->with('account')->first();

        $account = $merchant->account;
        $available = $account->available;

        if ($order->freeze_fee > $available) {
            throw new OrderException(new MessageBag(['资金账户可用余额不足']), 9001);
        }
        if ($order->freeze_fee <= 0) {
            throw new OrderException(new MessageBag(['工单费用异常，请刷新重试！']));
        }
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
        // 保内 // 安装单
        $order->install_fee = yuanToFen($order->install_fee);
        $order->price = $order->install_fee * $order->install_cnt;
        $order->is_inspect = 0;
        $order->fix_fee = 0;
        $order->is_fix = 0;
        $order->order_no = app('sequence')->generateOrderNo();
//        $order->merchant_brokerage = OrderBillingService::merchantBrokerage($order->price, intval($order->order_type), $order->merchant_id);
        $order->merchant_brokerage = 0;
        $order->urgent_fee = yuanToFen($order->urgent_fee);
        $order->price += $order->urgent_fee;
        $order->freeze_fee = app('MerchantService')->getCatPrice($order->middle_cat_id,$order->install_cnt); //预冻结费用
        $order->freeze_fee += $order->urgent_fee;
        $order->freeze_fee = ($order->price < $order->freeze_fee) ? $order->freeze_fee : $order->price;


        $merchant = $order->merchant()->with('account')->first();
        $account = $merchant->account;
        $available = $account->available;



        if ($order->freeze_fee > $available) {
            throw new OrderException(new MessageBag(['资金账户可用余额不足']), 9001);
        }
        if ($order->freeze_fee <= 0) {
            throw new OrderException(new MessageBag(['工单费用异常，请刷新重试！']));
        }
        Log::info($order->toArray(), [
            'f' => __METHOD__,
            'msg' => '查看创建order信息 end',
        ]);
        return $order->toArray();
    }
}
