<?php
namespace App\Services\Orders;

use App\Entities\MerchantBill;
use App\Entities\Order;
use App\Exceptions\OrderException;
use App\Repositories\MerchantUserRepository;
use App\Repositories\OrderFeeRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use App\Services\LuosidaoAccountService;
use App\Services\MerchantAccountService;
use App\Services\OrderBillingService;
use App\Services\PlatformAccountService;
use App\Services\WorkerAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Ixudra\Curl\Facades\Curl;

/**
 *  OrderService.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: OrderService.php 2017-03-21 下午4:57 $
 */
class OrderBaseService implements OrderServiceInterface
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
     * 用户
     *
     * @var \App\Repositories\OrderFeeRepository
     */
    public $orderFeeRepository;

    /**
     * @var \App\Repositories\PlatformAccountRepository
     */

    public $platformAccountService;

    public $platformAccountRepository;

    /**
     * @var \App\Repositories\LuosidaoAccountService
     */
    public $luosidaoAccountService;
    public static $service;



    /**
     * 构造函数
     *
     * @param \App\Repositories\OrderRepository $orderRepository
     * @param \App\Repositories\UserRepository $userRepository
     * @param \App\Repositories\MerchantUserRepository $merchantUserRepository
     */
    public function __construct(
        OrderRepository $orderRepository,
        MerchantAccountService $merchantAccountService,
        UserRepository $userRepository,
        WorkerAccountService $workerAccountService,
        OrderFeeRepository $orderFeeRepository,
        MerchantUserRepository $merchantUserRepository,
        PlatformAccountService $platformAccountService,
        LuosidaoAccountService $luosidaoAccountService
    ) {

        $this->orderFeeRepository = $orderFeeRepository;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->merchantAccountService = $merchantAccountService;
        $this->platformAccountService = $platformAccountService;
        $this->workerAccountService = $workerAccountService;
        $this->merchantUserRepository = $merchantUserRepository;
        $this->luosidaoAccountService = $luosidaoAccountService;
        $this->retryTimes = config('app.retry_times');
        $this->retrySleep = config('app.retry_sleep');
    }

    public static function getClassName()
    {
        return "OrderBaseService";
    }

    public static function getInstanceService($orderRepository, $merchantAccountService, $userRepository, $workerAccountService, $orderFeeRepository, $merchantUserRepository, $platformAccountService, $luosidaoAccountService)
    {
        $service = static::getClassName();
        if (!self::$service) {
            $className = 'App\\Services\\Orders\\' . $service;
            self::$service = new $className($orderRepository, $merchantAccountService, $userRepository, $workerAccountService, $orderFeeRepository, $merchantUserRepository, $platformAccountService, $luosidaoAccountService);
        }
        return self::$service;
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
        $fullAddress = $data['province'] . $data['city'] . $data['district'] . $data['address'];
        $geo = app('amap')->getLocation($fullAddress, $data['province'] . $data['city'] . $data['district']);
        $location = $geo->location;
        list($lng, $lat) = explode(',', $location);
        if (!isset($data['product_id']) || empty($data['product_id'])) {
            $data['product_id'] = 0; //拆分产品名称和型号
        }
        $userData = [
            'province' => $data['province'],
            'city' => $data['city'],
            'district' => $data['district'],
            'name' => $data['user_name'],
            'address' => $data['address'],
            'full_address' => $fullAddress,
            'lng' => $lng,
            'lat' => $lat,

        ];

        $user = $this->userRepository->userFirstOrCreate(['mobile' => $data['user_mobile']], $userData);

        $data['user_id'] = $user->id;
        $data['full_address'] = $fullAddress;
        $data['user_lng'] = $lng;
        $data['user_lat'] = $lat;

        Log::info('查看创建order信息', [$data]);
        $order = $this->orderRepository->create($data);

        return $order;
    }

    public function updateUserInfo($userId, $data)
    {
        $fullAddress = $data['province'] . $data['city'] . $data['district'] . $data['address'];
        $geo = app('amap')->getLocation($fullAddress, $data['province'] . $data['city'] . $data['district']);
        $location = $geo->location;
        list($lng, $lat) = explode(',', $location);
        if (!isset($data['product_id']) || empty($data['product_id'])) {
            $data['product_id'] = 0; //拆分产品名称和型号
        }
        $userData = [
            'province' => $data['province'],
            'city' => $data['city'],
            'district' => $data['district'],
            'name' => $data['user_name'],
            'address' => $data['address'],
            'full_address' => $fullAddress,
            'lng' => $lng,
            'lat' => $lat,

        ];
        $user = $this->userRepository->update($userData, $userId);
    }


    //发布工单之后调用，增加商户用户
    public function addMerchantUser($order)
    {
        $context = [
            'order' => $order->toArray(),
            'method' => __METHOD__,
            'msg' => '增加商户用户',
        ];
        try {
            //添加统计数据
            $merchantUserData = [
                'merchant_id' => $order->merchant_id,
                'user_id' => $order->user_id,
                'product_id' => $order->product_id,
                'order_type' => $order->order_type,
                'biz_type' => $order->biz_type,
                'published_at' => date('Y-m-d H:i:s'),
            ];
            $this->merchantUserRepository->userFirstOrCreate($merchantUserData, $merchantUserData);

            $userWhere = [
                'merchant_id' => $order->merchant_id,
                'user_id' => $order->user_id,
            ];
            $this->merchantUserRepository->increment($userWhere);

            // 发布订单+1
            $this->editMerchantOrderNum($order->id, 'publish');
        } catch (\Exception $ex) {
            Log::error($ex, $context);
        }
    }

    /**
     * 确认工单之后，商户的数目完成增加1，进行中减去1
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param $id
     *
     * @return mixed
     * @throws \App\Exceptions\MerchantAccountException
     * @throws \App\Exceptions\MerchantBillException
     * @throws \App\Exceptions\OrderException
     * @throws \App\Exceptions\PlatformAccountException
     * @throws \App\Exceptions\PlatformBillException
     * @throws \App\Exceptions\WorkerAccountException
     * @throws \App\Exceptions\WorkerBillException
     */
    public function editMerchantOrderNum($id, $type = 'confirm')
    {
        $fields = [
            'id',
            'price',
            'inspect_fee',
            'fix_fee',
            'install_fee',
            'install_cnt',
            'worker_id',
            'merchant_brokerage',
            'worker_brokerage',
            'worker_fee',
            'freeze_fee',
            'order_type',
            'merchant_id',
            'mode_status',
        ];

        $order = $this->orderRepository->find($id, $fields);

        switch ($type) {
            case 'confirm':
                # code...
                // 完成+1
                $order->merchant()->increment('success_order_cnt');
                // 进行中-1
                $order->merchant()->decrement('doing_order_cnt');

                break;
            case 'cancel':
                // 完成+1
                $order->merchant()->increment('cancel_order_cnt');
                // 进行中-1
                $order->merchant()->decrement('doing_order_cnt');

                $userWhere = [
                    'user_id' => $order->user_id,
                    'merchant_id' => $order->merchant_id,
                ];
                $this->merchantUserRepository->decrement($userWhere);
                break;
            case 'publish':
                $order->merchant()->increment('doing_order_cnt');
                break;
            default:
                # code...
                break;
        }
        return true;
    }


    /**
     * 改派工单
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param $id
     * @param $workerId
     * @param $workerName
     * @param $workerMobile
     *
     * @throws \App\Exceptions\OrderException
     */
    public function reassign($id, $workerId = 0, $workerName = '', $workerMobile = '')
    {
        // TODO: Implement reassign() method.
        $beforeOrder = $this->orderRepository->find($id, [
            'id',
            'worker_id',
        ]);

        $reassigned = $this->orderRepository->reassign($id, $workerId, $workerName, $workerMobile);
        if (!$reassigned) {
            throw new OrderException(new MessageBag(['改派工单失败']));
        }

        $afterOrder = $this->orderRepository->find($id, [
            'id',
            'worker_id',
        ]);

        $beforeOrder->worker()->decrement('order_doing_cnt');
        $afterOrder->worker()->increment('order_doing_cnt');
    }

    /**
     * 获取取消费用
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param $id
     *
     * @return int
     * @throws \App\Exceptions\OrderException
     */
    public function getCancelFee($id)
    {
        $fields = [
            'id',
            'price',
            'state',
            'fix_fee',
            'inspect_fee',
            'worker_brokerage',
            'merchant_brokerage',
            'merchant_id',
            'worker_id',
            'order_type',
            'mode_status'
        ];


        $order = $this->orderRepository->find($id, $fields);

        $price = intval($order->price);

        $state = intval($order->state);
        $orderType = intval($order->order_type);

        if ($orderType === 0 && $state >= Order::INSERVICE) {
            throw new OrderException(new MessageBag(['服务中的工单不能取消']));
        }

        $cancelFee = 0;
        switch ($state) {
            case Order::WAIT_PUBLISHED:
            case Order::PUBLISHED:
                $cancelFee = 0;
                break;
            case Order::ACCEPTED:
            case Order::BOOKED:
                $cancelFee = OrderBillingService::merchantCancelFee($price, $orderType);
                break;
            case Order::INSERVICE:
                if ($orderType === 0) {
                    throw new OrderException(new MessageBag(['服务中的工单不能取消']));
                } else {
                    $cancelFee = OrderBillingService::merchantCancelFee($price, $orderType);
                }
                break;
            case Order::CANCELED:
                throw new OrderException(new MessageBag(['工单已经被取消了']));
                break;
            default:
                throw new OrderException(new MessageBag(['无意义的取消操作']));
        }

        return fenToYuan($cancelFee);
    }

    /**
     * 发送配件
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param        $id
     * @param        $partDesc
     * @param        $partFrom
     * @param        $partName
     * @param string $expressName
     * @param string $expressNo
     * @param int $partPrice
     *
     * @return \App\Entities\OrderPart
     * @throws \App\Exceptions\OrderPartException
     */
    public function addParts(Request $request,$id, $partId = 0)
    {
    }

    /**
     * 追加维修费用
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param        $id
     * @param        $fixFee
     * @param string $extraDesc
     * @param string $smallCat
     *
     * @return \App\Entities\OrderFee|false|\Illuminate\Database\Eloquent\Model
     * @throws \App\Exceptions\OrderException
     */
    public function addFixFee($id, $fixFee = 0, $extraDesc = '', $smallCat = '', $feeId = 0)
    {
    }

    /**
     * 添加配件费
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param     $id
     * @param int $partPrice
     *
     * @return \App\Entities\OrderFee|false|\Illuminate\Database\Eloquent\Model
     * @throws \App\Exceptions\MerchantAccountException
     * @throws \App\Exceptions\MerchantBillException
     * @throws \App\Exceptions\OrderException
     */
    public function addPartFee($id, $partPrice = 0, $partDesc = '', $feeId = 0, $upOrDown = 'online', $partId = 0)
    {
    }

    /**
     * 追加其他费用
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param        $id
     * @param        $extraFee
     * @param string $extraDesc
     *
     * @return \App\Entities\OrderFee|false|\Illuminate\Database\Eloquent\Model
     * @throws \App\Exceptions\MerchantAccountException
     * @throws \App\Exceptions\MerchantBillException
     * @throws \App\Exceptions\OrderException
     * @throws \App\Exceptions\OrderFeeException
     */
    public function addExtraFee($id, $extraFee = 0, $extraDesc = '', $feeId = 0)
    {
    }

    public function addInspectFee($id, $inspectFee = 0)
    {
    }

    public function removeInspectFee($id, $inspectFee = 0)
    {
    }

    //查看order的信息
    public function findByOrderId($id, $columns = ['*'])
    {
        return $this->orderRepository->find($id, $columns);
    }

    public function call($id)
    {
        $fields = [
            'id',
            'merchant_tel',
            'worker_mobile',
        ];
        $order = $this->orderRepository->find($id, $fields);

        $privacyCall = $order->getPrivacyCall();

        $response = Curl::to($privacyCall)->get();

        return $response;
    }

    /**
     * check资金账户
     *
     * @param  array $data
     *
     * @return array
     * @throws \App\Exceptions\OrderException
     */
    public function checkMerchantAccount(Order $order, $amount = 0)
    {
        // TODO: Implement checkMerchantAccount() method.
        $account = $order->merchant->account()->lockForUpdate()->first();
        $available = $account->available;

        if ($amount > $available) {
            throw new OrderException(new MessageBag(['资金账户可用余额不足']), 9001);
        }
    }


    /**
     * check工单预冻结费用是否小于工单总费用
     *
     * @param  array $data
     *
     * @return array
     * @throws \App\Exceptions\OrderException
     */
    public function checkFreezeFee(Order $order, $amount ,$freezeFee)
    {
        $fee  =   $amount - $freezeFee ; //若追加费用后，工单金额大于预冻结费用，追加冻结
        if($fee > 0){
            $this->checkMerchantAccount($order, $fee); //验证余额

            $merchantAccountService = $this->merchantAccountService;
            retry(
                $this->retryTimes,
                function () use ($merchantAccountService, $order, $fee) {
                    $merchantId = $order->merchant_id;
                    Log::notice('追加费用预冻结', [$fee,__METHOD__]);
                    $bizType = 'FREEZE_' . strtoupper('freeze_fee');
                    $bizTypeValue = constant(sprintf('%s::%s', MerchantBill::class, $bizType));
                    $merchantAccountService->freeze($merchantId, $fee, $order, $bizTypeValue);
                },
                $this->retrySleep
            );

            return $fee;
        }

        return 0;
    }


    /**
     * 管理后台修改工单费用明细
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param \App\Entities\Order $order
     * @param                     $feeId
     * @param                     $state
     *
     */
    public function updateOrderFee(Order $order, $feeId, $state)
    {
    }


    /**
     * 后台取消费用处理商家资金解冻和流水
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param        $order
     * @param        $fee
     * @param string $bizComment
     *
     * @return \App\Entities\OrderFee|false|\Illuminate\Database\Eloquent\Model
     * @throws \App\Exceptions\MerchantAccountException
     * @throws \App\Exceptions\MerchantBillException
     * @throws \App\Exceptions\OrderException
     * @throws \App\Exceptions\OrderFeeException
     */
    public function cancelFixFee(Order $order, $fee, $bizComment)
    {
    }


    public function addOrderFee(Order $order, $fee, $field, $extraDesc = '', $feeId = 0, $upOrDown = 'online', $partId = 0, $belongType = 1)
    {
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
    public function update($id, $data = [])
    {
    }

    /**
     * 取消工单
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param $id
     * @param $cancelReason
     * @param $cancelableId
     * @param $cancelableType
     *
     * @throws \App\Exceptions\MerchantAccountException
     * @throws \App\Exceptions\MerchantBillException
     * @throws \App\Exceptions\OrderException
     * @throws \App\Exceptions\PlatformAccountException
     * @throws \App\Exceptions\PlatformBillException
     * @throws \App\Exceptions\WorkerAccountException
     * @throws \App\Exceptions\WorkerBillException
     */
    public function cancel($id, $cancelReason = '', $cancelableId = '', $cancelableType = '')
    {
    }

    /**
     * 发布工单
     *
     * 自行处理发布工单之前，需要给工单添加的费用，addOrderFee
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param $id
     *
     * @return mixed
     * @throws \App\Exceptions\MerchantAccountException
     * @throws \App\Exceptions\MerchantBillException
     * @throws \App\Exceptions\OrderException
     */
    public function publish($id)
    {
    }

    public function confirm($id)
    {
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
    }
}
