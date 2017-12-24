<?php
namespace App\Services\Orders;

use App\Entities\LuosidaoBill;
use App\Entities\Merchant;
use App\Entities\MerchantBill;
use App\Entities\Order;
use App\Entities\OrderFee;
use App\Entities\OrderPart;
use App\Entities\WorkerBill;
use App\Events\OrderCanceledEvent;
use App\Exceptions\BaseException;
use App\Exceptions\OrderException;
use App\Exceptions\OrderFeeException;
use App\Exceptions\OrderPartException;
use App\Services\OrderBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;

/**
 *  OrderService.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: OrderService.php 2017-03-21 下午4:57 $
 */
class OrderBaseInService extends OrderBaseService
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
     * @var \App\Repositories\PlatformAccountRepository
     */
    public $platformAccountRepository;

    public $platformAccountService;
    public $luosidaoAccountService;


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
     * @throws \App\Exceptions\WorkerAccountException
     * @throws \App\Exceptions\WorkerBillException
     */
    public function cancel($id, $cancelReason = '', $cancelableId = '', $cancelableType = '')
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
            'freeze_fee',
            'order_type',
            'user_id',
        ];


        $order = $this->orderRepository->find($id, $fields);

        $price = intval($order->price);
        $brokerage = intval($order->merchant_brokerage);
        $merchantId = $order->merchant_id;
        $workerId = $order->worker_id;
        $unfreezedFee = $order->freeze_fee;
        $orderType = intval($order->order_type);
//        Log::info('orders:' . json_encode($order), $context);

        $state = intval($order->state);
        if ($orderType === 0 && $state >= Order::INSERVICE) {
            throw new OrderException(new MessageBag(['服务中的工单不能取消']));
        }
        $platformCancelCost = 0;
        $workerCancelCost = 0;
        switch ($state) {
            case Order::WAIT_PUBLISHED:
            case Order::PUBLISHED:
                $merchantCancelFee = 0;
                break;
            case Order::ACCEPTED:
            case Order::BOOKED:
                $merchantCancelFee = OrderBillingService::merchantCancelFee($price, $orderType);
                // 完成+1
                $order->worker()->increment('cancel_order_cnt');
                // 进行中-1
                $order->worker()->decrement('doing_order_cnt');
                break;
            case Order::INSERVICE:
                if ($orderType === 0) {
                    throw new OrderException(new MessageBag(['服务中的工单不能取消']));
                } else {
                    $merchantCancelFee = OrderBillingService::merchantCancelFee($price, $orderType);
                    // 完成+1
                    $order->worker()->increment('cancel_order_cnt');
                    // 进行中-1
                    $order->worker()->decrement('doing_order_cnt');
                }
                break;
            case Order::CANCELED:
                throw new OrderException(new MessageBag(['工单已经被取消了']));
                break;
            default:
                throw new OrderException(new MessageBag(['服务中的工单不能取消']));
        }

        // 订单状态更新
        $canceled = $this->orderRepository->cancel(
            $id,
            $state,
            $cancelReason,
            $platformCancelCost,
            $workerCancelCost,
            $cancelableId,
            $cancelableType
        );
        if (!$canceled) {
            throw new OrderException(new MessageBag(['取消工单失败']));
        }

        event(new OrderCanceledEvent($id));

        $merchantAccountService = $this->merchantAccountService;
        $workerAccountService = $this->workerAccountService;
        $platformAccountService = $this->platformAccountService;

        //解冻商家资金
        $merchantAccountModel = retry(
            $this->retryTimes,
            function () use ($merchantAccountService, $merchantId, $unfreezedFee, $order) {
                $bizType = MerchantBill::UNFREEZE;
                $merchantAccountService->unfreeze($merchantId, $unfreezedFee, $order, $bizType);
            },
            $this->retrySleep
        );


        // 扣除商家资金
        if ($merchantCancelFee > 0) {
            // 支付工单费用
            $payoutAmount = $merchantCancelFee + $brokerage;

            retry(
                $this->retryTimes,
                function () use ($merchantAccountService, $merchantId, $payoutAmount, $order) {
                    $merchantAccountService->decrement($merchantId, $payoutAmount, $order);
                },
                $this->retrySleep
            );

            //师傅收入
            retry(
                $this->retryTimes,
                function () use ($workerAccountService, $workerId, $workerCancelCost, $order) {
                    $bizType = WorkerBill::INCOME;
                    $workerAccountService->increment($workerId, $workerCancelCost, $order, $bizType);
                },
                $this->retrySleep
            );

            //螺丝刀资金处理
            $luosidaoAccountId = config('luosidao.luosidao_account_id');
            $luosidaoAccountService = $this->luosidaoAccountService;
            retry(
                $this->retryTimes,
                function () use ($luosidaoAccountService, $luosidaoAccountId, $platformCancelCost, $brokerage, $order) {
                    //商家佣金
                    if ($brokerage > 0) {
                        $bizType = LuosidaoBill::MERCHANT_ORDER_BROKERAGE;
                        $luosidaoAccountService->increment($luosidaoAccountId, $brokerage, $order, $bizType);
                    }
                    //商家取消罚金
                    if ($platformCancelCost > 0) {
                        $bizType = LuosidaoBill::ORDER_CANCEL_FINE;
                        $luosidaoAccountService->increment($luosidaoAccountId, $platformCancelCost, $order, $bizType);
                    }
                },
                $this->retrySleep
            );
        } else {
            $response = app('lbs')->syncOrder($id, 1);
            $context = [
                'id' => $id,
                'method' => __METHOD__,
            ];
            Log::info(json_encode($response), $context);
        }

        $orderFeeData = [
            'state' => '-2',
        ];
        if ($state >= Order::PUBLISHED) {
            $feeSaved = $order->orderFees()->update($orderFeeData);
            if (!$feeSaved) {
                throw new OrderException(new MessageBag(['费用删除失败']));
            }
        }
        $this->editMerchantOrderNum($id, 'cancel');
    }


    /**
     * 确认工单
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
     * @throws \App\Exceptions\WorkerAccountException
     * @throws \App\Exceptions\WorkerBillException
     */
    public function getConfirmFee($id)
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
        $orderType = intval($order->order_type);
        $merchantId = intval($order->merchant_id);
        $fixFee = intval($order->fix_fee);
        $inspectFee = intval($order->inspect_fee);
        $installFee = intval($order->install_fee);
        $installCnt = intval($order->install_cnt);
        $price = intval($order->price);
        $freezeFee = intval($order->freeze_fee);

        $BrokerageAmount = $fixFee + ($installFee * $installCnt) + $inspectFee;
        //计算商家佣金
        $merchantBrokerage = OrderBillingService::merchantBrokerage($BrokerageAmount, $orderType, $merchantId);

        $data = [
            'fee_all' => fenToYuan($price + $merchantBrokerage),
            'brokerage' => fenToYuan($merchantBrokerage),
        ];
        return $data;
    }


    /**
     * 确认工单
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
     * @throws \App\Exceptions\WorkerAccountException
     * @throws \App\Exceptions\WorkerBillException
     */
    public function confirm($id)
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
        $parts = $order->parts()->where('parent_id',0)->whereIn('state', [0, 2, 3, 4, 5])->count();
        if ($parts) {
            throw new OrderException(new MessageBag(['还有进行中的配件未完成']));
        }
        $orderType = intval($order->order_type);
        $workerId = intval($order->worker_id);
        $merchantId = intval($order->merchant_id);
        $fixFee = intval($order->fix_fee);
        $inspectFee = intval($order->inspect_fee);
        $installFee = intval($order->install_fee);
        $installCnt = intval($order->install_cnt);
        $merchantBrokerageOut = intval($order->merchant_brokerage);
        $price = intval($order->price);
        $freezeFee = intval($order->freeze_fee);

        $BrokerageAmount = $fixFee + ($installFee * $installCnt) + $inspectFee;

        //计算商家佣金
        $merchantBrokerage = OrderBillingService::merchantBrokerage($BrokerageAmount, $orderType, $merchantId);

        $amount = $price + $merchantBrokerage;
        $addFreezeFee = $this->checkFreezeFee($order, $amount, $freezeFee);
        $freezeFee += $addFreezeFee;

        //查询费用列表中佣金数据并修改
        $brokerageFeeModel = $order->orderFees()->where('fee_type', OrderFee::MERCHANT_BROKERAGE)->where('state', 1)->first();
        $feeId = count($brokerageFeeModel) ? $brokerageFeeModel->id : 0;
        $this->addOrderFee($order, $merchantBrokerage, 'merchant_brokerage', '', $feeId);


        //计算收取师傅佣金
        $workerBrokerage = OrderBillingService::workerBrokerage($BrokerageAmount);

        $workerFee = $price - $workerBrokerage;
        $payload['merchant_brokerage'] = $merchantBrokerage;
        $payload['worker_brokerage'] = $workerBrokerage;
//        $payload['freeze_fee'] = $freezeFee;

        //产品要求结单后清0
        $payload['freeze_fee'] = 0;
        $payload['worker_fee'] = $workerFee;
        $payload['pay_state'] = 1;
        //确认工单
        $orderUpdated = $this->orderRepository->confirm($id, $payload);
        if (!$orderUpdated) {
            throw new OrderException(new MessageBag(['确认订单失败']));
        }

        Log::notice([
            'id' => $id,
            'msg' => '保内工单结算费用核对',
            '冻结总金额' => $freezeFee,
            '师傅需要抽佣总费用' => $BrokerageAmount,
            '师傅最后获得' => $workerFee,
            'order price' => $price,
            '商家佣金' => $merchantBrokerage,
        ], [__METHOD__]);
        // 保内安装师傅资金账户处理

        $workerAccountService = $this->workerAccountService;
        $workerAccount = retry(
            $this->retryTimes,
            function () use ($workerAccountService, $workerId, $workerFee, $order) {
                $bizType = WorkerBill::INCOME;
                $workerAccountService->increment($workerId, $workerFee, $order, $bizType);
            },
            $this->retrySleep
        );


        //螺丝刀资金账户处理
        $luosidaoAccountId = config('luosidao.luosidao_account_id');
        $luosidaoAccountService = $this->luosidaoAccountService;
        retry(
            $this->retryTimes,
            function () use ($luosidaoAccountService, $luosidaoAccountId, $workerBrokerage, $merchantBrokerage, $order) {
                //师傅佣金收入
                if ($workerBrokerage > 0) {
                    $bizType = LuosidaoBill::WORKER_ORDER_BROKERAGE;
                    $luosidaoAccountService->increment($luosidaoAccountId, $workerBrokerage, $order, $bizType);
                }
                //商家佣金
                if ($merchantBrokerage > 0) {
                    $bizType = LuosidaoBill::MERCHANT_ORDER_BROKERAGE;
                    $luosidaoAccountService->increment($luosidaoAccountId, $merchantBrokerage, $order, $bizType);
                }
            },
            $this->retrySleep
        );

        // 商户资金账户处理
        // 解冻商家资金，冻结的费用里包括检测费，检测费需要配件的情况下才会收取
        $merchantAccountService = $this->merchantAccountService;
        retry(
            $this->retryTimes,
            function () use ($merchantAccountService, $merchantId, $freezeFee, $order) {
                $bizType = MerchantBill::UNFREEZE;
                return $merchantAccountService->unfreeze($merchantId, $freezeFee, $order, $bizType);
            },
            $this->retrySleep
        );


        // 支付工单费用 // 真正支付的费用
        $payoutFee = ($workerFee + $merchantBrokerage + $workerBrokerage);
        retry(
            $this->retryTimes,
            function () use ($merchantAccountService, $merchantId, $payoutFee, $order) {
                return $merchantAccountService->decrement($merchantId, $payoutFee, $order);
            },
            $this->retrySleep
        );

        $this->editMerchantOrderNum($id, 'confirm');
        return $order;
    }

    /**
     * 待发布工单
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
    public function waitPublish($id)
    {
        $updated = $this->orderRepository->waitPublish($id);
        if (!$updated) {
            throw new OrderException(new MessageBag(['工单发布失败']));
        }

        $fields = [
            'id',
            'price',
            'merchant_name',
            'merchant_id',
            'merchant_logo',
            'user_lng',
            'user_lat',
            'full_address',
            'province',
            'city',
            'district',
            'address',
            'order_no',
            'order_type',
            'big_cat',
            'small_cat',
            'middle_cat',
            'published_at',
            'inspected_at',
            'biz_type',
            'inspect_fee',
            'fix_fee',
            'cancel_fee',
            'freeze_fee',
            'worker_brokerage',
            'merchant_brokerage',
            'worker_fee',
            'extra_fee',
            'part_fee',
            'urgent_fee',
            'is_sync',
            'mode_status',
        ];
        $order = $this->orderRepository->find($id, $fields);
        return $order;
    }


    /**
     * 审核后发布工单
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
    public function savePublish($id)
    {
        $fields = [
            'id',
            'price',
            'order_no',
            'merchant_id',
            'merchant_name',
            'merchant_tel',
            'merchant_logo',
            'product_id',
            'small_cat',
            'user_id',
            'user_lng',
            'user_lat',
            'user_mobile',
            'user_name',
            'full_address',
            'order_desc',
            'order_type',
            'published_at',
            'fix_fee',
            'freeze_fee',
            'inspect_fee',
            'urgent_fee',
            'merchant_brokerage',
            'worker_brokerage',
            'biz_type',
            'install_fee',
            'install_cnt',
            'product_id',
            'mode_status',
            'dealer_id',
        ];
        $order = $this->orderRepository->find($id, $fields);
        Log::info('查看创建order信息', $order->toArray());
        $price = $order->price;
        Log::info($order->toArray(), ['msg' => '发布工单数据', 'f' => __METHOD__]);
        $brokerage = $order->merchant_brokerage;
        $bizType = intval($order->biz_type);
        $orderType = intval($order->order_type);
        // 检测费+维修费+平台佣金
        $amount = $price + $brokerage;
        $freezeFee = intval($order->freeze_fee);

        $updated = $this->orderRepository->savePublish($id);
        if (!$updated) {
            throw new OrderException(new MessageBag(['工单发布失败']));
        }

        $merchantAccountService = $this->merchantAccountService;
        retry(
            $this->retryTimes,
            function () use ($merchantAccountService, $order, $freezeFee) {
                $merchantId = $order->merchant_id;
                Log::info('查看创建order信息 预冻结', [$freezeFee]);
                $bizType = 'FREEZE_' . strtoupper('freeze_fee');
                $bizTypeValue = constant(sprintf('%s::%s', MerchantBill::class, $bizType));
                $merchantAccountService->freeze($merchantId, $freezeFee, $order, $bizTypeValue);
            },
            $this->retrySleep
        );

        // 保存费用清单
        foreach ([
                     'fix_fee',
                     'inspect_fee',
                     'urgent_fee',
                     'merchant_brokerage',
                     'install_fee',
                 ] as $field) {
            if (isset($order->$field) && intval($order->$field) > 0) {
                if ($field === 'install_fee') {
                    $fee = intval($order->$field) * intval($order->install_cnt);
                } else {
                    $fee = intval($order->$field);
                }

                //添加保养清洗费说明
                if ($bizType == 3 && $field == 'install_fee') {
                    $field = 'cleaning_fee';
                } elseif ($bizType == 2 && $field == 'install_fee') {
                    $field = 'maintenance_fee';
                }
                $this->addOrderFee($order, $fee, $field);
            }
        }

        $this->addMerchantUser($order);
        $fields = [
            'id',
            'price',
            'merchant_name',
            'merchant_id',
            'merchant_logo',
            'user_lng',
            'user_lat',
            'full_address',
            'province',
            'city',
            'district',
            'address',
            'order_no',
            'order_type',
            'big_cat',
            'small_cat',
            'middle_cat',
            'published_at',
            'inspected_at',
            'biz_type',
            'inspect_fee',
            'fix_fee',
            'cancel_fee',
            'freeze_fee',
            'worker_brokerage',
            'merchant_brokerage',
            'worker_fee',
            'extra_fee',
            'part_fee',
            'urgent_fee',
            'is_sync',
            'mode_status',
        ];
        $order = $this->orderRepository->find($id, $fields);
        return $order;
    }


    /**
     * 发布工单
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
        $fields = [
            'id',
            'price',
            'order_no',
            'merchant_id',
            'merchant_name',
            'merchant_tel',
            'merchant_logo',
            'product_id',
            'small_cat',
            'user_id',
            'user_lng',
            'user_lat',
            'user_mobile',
            'user_name',
            'full_address',
            'order_desc',
            'order_type',
            'published_at',
            'freeze_fee',
            'fix_fee',
            'inspect_fee',
            'urgent_fee',
            'merchant_brokerage',
            'worker_brokerage',
            'biz_type',
            'install_fee',
            'install_cnt',
            'product_id',
            'mode_status',
            'dealer_id',
        ];
        $order = $this->orderRepository->find($id, $fields);
        Log::info('查看创建order信息', $order->toArray());
        $price = $order->price;
        Log::info($order->toArray(), ['msg' => '发布工单数据', 'f' => __METHOD__]);
        $brokerage = $order->merchant_brokerage;
        $bizType = intval($order->biz_type);
        $orderType = intval($order->order_type);
        // 检测费+维修费+平台佣金
//        $amount = $price + $brokerage;
        $freezeFee = intval($order->freeze_fee);

        $updated = $this->orderRepository->publish($id);
        if (!$updated) {
            throw new OrderException(new MessageBag(['工单发布失败']));
        }

        $merchantAccountService = $this->merchantAccountService;
        retry(
            $this->retryTimes,
            function () use ($merchantAccountService, $order, $freezeFee) {
                $merchantId = $order->merchant_id;
                Log::info('查看创建order信息 预冻结', [$freezeFee]);
                $bizType = 'FREEZE_' . strtoupper('freeze_fee');
                $bizTypeValue = constant(sprintf('%s::%s', MerchantBill::class, $bizType));
                $merchantAccountService->freeze($merchantId, $freezeFee, $order, $bizTypeValue);
            },
            $this->retrySleep
        );

        // 保存费用清单
        foreach ([
                     'fix_fee',
                     'inspect_fee',
                     'urgent_fee',
                     'merchant_brokerage',
                     'install_fee',
                 ] as $field) {
            if (isset($order->$field) && intval($order->$field) > 0) {
                if ($field === 'install_fee') {
                    $fee = intval($order->$field) * intval($order->install_cnt);
                } else {
                    $fee = intval($order->$field);
                }

                //添加保养清洗费说明
                if ($bizType == 3 && $field == 'install_fee') {
                    $field = 'cleaning_fee';
                } elseif ($bizType == 2 && $field == 'install_fee') {
                    $field = 'maintenance_fee';
                }
                $this->addOrderFee($order, $fee, $field);
            }
        }

        $this->addMerchantUser($order);
        $fields = [
            'id',
            'price',
            'merchant_name',
            'merchant_id',
            'merchant_logo',
            'user_lng',
            'user_lat',
            'full_address',
            'province',
            'city',
            'district',
            'address',
            'order_no',
            'order_type',
            'big_cat',
            'small_cat',
            'middle_cat',
            'published_at',
            'inspected_at',
            'biz_type',
            'inspect_fee',
            'fix_fee',
            'cancel_fee',
            'freeze_fee',
            'worker_brokerage',
            'merchant_brokerage',
            'worker_fee',
            'extra_fee',
            'part_fee',
            'urgent_fee',
            'is_sync',
            'mode_status',
        ];
        $order = $this->orderRepository->find($id, $fields);
        return $order;
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
        $orderFee = $order->orderFees()->where('id', '=', $feeId)->first();
        Log::notice($orderFee, ['msg' => "修改工单費用信息", 'f' => __METHOD__]);
        $fee = intval($orderFee->fee);
        $feeState = intval($orderFee->state);
        $feeType = intval($orderFee->fee_type);
        $smallCat = $orderFee->small_cat;
        $state = intval($state);
        if ($feeState === $state) {
            throw new \Exception('重复提交');
        }
        $orderId = $order->id;
        $fields = [
            'id',
            'price',
            'inspect_fee',
            'fix_fee',
            'cancel_fee',
            'freeze_fee',
            'worker_brokerage',
            'merchant_brokerage',
            'worker_fee',
            'extra_fee',
            'distance_fee',
            'freight_fee',
            'express_fee',
            'part_fee',
            'urgent_fee',
            'install_fee',
            'order_type',
            'is_part',
            'is_inspect',
            'is_fix',
            'mode_status',
        ];

        foreach ($fields as $field) {
            if (isset($order->{$field})) {
                $order->{$field} = intval($order->{$field});
            }
        }
        $price = $order->price;   //总费用
        $fixFee = $order->fix_fee; //维修费
        $inspectFee = $order->inspect_fee; //上门费
        $freezeFee = $order->freeze_fee; //冻结总金额
        $extraFee = $order->extra_fee; //其他费用
        $distanceFee = $order->distance_fee; //远程费
        $freightFee = $order->freight_fee; //拉修费
        $expressFee = $order->express_fee; //快递费
        $partFee = $order->part_fee;  //配件费用
        $installFee = $order->install_fee;  //安装费用
        $installCnt = intval($order->install_cnt);
        if ($state === 1) {
            // 确认费用
            $fee = fenToYuan(intval($fee));
            switch ($feeType) {
                case OrderFee::INSPECT_FEE:
                    return $this->addInspectFee($orderId, $fee, '', $feeId);
                case OrderFee::FIX_FEE:
                    return $this->addFixFee($orderId, $fee, '', $smallCat, $feeId);
                case OrderFee::MAINTENANCE_FEE:
                    return $this->addInstallFee($orderId, $fee, '', $smallCat, $feeId);
                case OrderFee::INSTALL_FEE:
                    return $this->addInstallFee($orderId, $fee, '', $smallCat, $feeId);
                case OrderFee::CLEANING_FEE:
                    return $this->addInstallFee($orderId, $fee, '', $smallCat, $feeId);
                case OrderFee::EXTRA_FEE:
                    return $this->addExtraFee($orderId, $fee, '', $feeId);
                case OrderFee::DISTANCE_FEE:
                    return $this->addDistanceFee($orderId, $fee, '', $feeId);
                case OrderFee::FREIGHT_FEE:
                    return $this->addFreightFee($orderId, $fee, '', $feeId);
                case OrderFee::EXPRESS_FEE:
                    return $this->addExpressFee($orderId, $fee, '', $feeId);
                case OrderFee::PART_FEE:
                    return $this->addPartFee($orderId, $fee, '', $feeId);
                default:
                    throw new OrderException(new MessageBag(['追加费用类型未知']));
                    break;
            }
        } elseif ($state === -1) {
            $orderFee->state = $state;
            $orderFee->save();
            //未确认费用取消直接修改状态
            if ($feeState === 0) {
                return $orderFee;
            }

            // 解冻金额，修改状态，处理工单资金和商家流水
            switch ($feeType) {
                //取消已确认的检测费
                case OrderFee::INSPECT_FEE:
                    $updated = $this->updateFee('inspect_fee', $inspectFee, $fee, $freezeFee, $price, $orderId, $installCnt);
                    break;
                //取消已确认的维修费
                case OrderFee::FIX_FEE:
//                    $this->cancelFixFee($order, $fee, '解冻维修费');
                    $updated = $this->updateFee('fix_fee', $fixFee, $fee, $freezeFee, $price, $orderId, $installCnt);
                    break;
                //取消已确认的其他费
                case OrderFee::EXTRA_FEE:
//                    $this->cancelExtraFee($order, $fee, '解冻其他费');
                    $updated = $this->updateFee('extra_fee', $extraFee, $fee, $freezeFee, $price, $orderId, $installCnt);
                    break;
                //取消已确认的远程费
                case OrderFee::DISTANCE_FEE:
                    $updated = $this->updateFee('distance_fee', $distanceFee, $fee, $freezeFee, $price, $orderId, $installCnt);
                    break;
                //取消已确认的拉修费
                case OrderFee::FREIGHT_FEE:
                    $updated = $this->updateFee('freight_fee', $freightFee, $fee, $freezeFee, $price, $orderId, $installCnt);
                    break;
                //取消已确认的快递费
                case OrderFee::EXPRESS_FEE:
                    $updated = $this->updateFee('express_fee', $expressFee, $fee, $freezeFee, $price, $orderId, $installCnt);
                    break;
                //取消已确认的配件费
                case OrderFee::PART_FEE:
//                    $this->cancelPartFee($order, $fee, '解冻配件费');
                    $updated = $this->updateFee('part_fee', $partFee, $fee, $freezeFee, $price, $orderId, $installCnt);
                    break;
                //取消已确认的安装费
                case OrderFee::INSTALL_FEE:
//                    $this->cancelInstallFee($order, $fee, '解冻安装费');
                    $updated = $this->updateFee('install_fee', $installFee, $fee, $freezeFee, $price, $orderId, $installCnt);
                    break;
                //取消已确认的保养费
                case OrderFee::MAINTENANCE_FEE:
//                    $this->cancelInstallFee($order, $fee, '解冻保养费');
                    $updated = $this->updateFee('install_fee', $installFee, $fee, $freezeFee, $price, $orderId, $installCnt);
                    break;
                //取消已确认的清洗费
                case OrderFee::CLEANING_FEE:
                    $updated = $this->updateFee('install_fee', $installFee, $fee, $freezeFee, $price, $orderId, $installCnt);
                    break;
                //取消已确认的清洗费
                case OrderFee::SYSTEM_INSPECT_FEE:
                    throw new OrderException(new MessageBag(['该费用为系统追加费用，不可拒绝']));
                    break;
                default:
                    throw new OrderException(new MessageBag(['取消费用类型未知']));
                    break;
            }
            return $updated;
        }
    }


    /**
     * 后台取消维修费用处理商家资金解冻和流水
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
        $merchantId = $order->merchant_id;
        // 解冻商家资金
        $bizType = MerchantBill::UNFREEZE_FIX_FEE;

        $this->merchantAccountService->unfreeze($merchantId, $fee, $order, $bizType);

        Log::info('解冻商家资金', ['msg' => '添加取消费用资金解冻记录成功', 'bizComment' => $bizComment, 'function' => __METHOD__]);

        return true;
    }


    /**
     * 后台取消安装检测费用处理商家资金解冻和流水
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
    public function cancelInstallFee(Order $order, $fee, $bizComment)
    {
        $merchantId = $order->merchant_id;
        $orderBizType = intval($order->biz_type);
        // 解冻商家资金
        switch ($orderBizType) {
            case 0: //解冻安装
                $bizType = MerchantBill::UNFREEZE_INSTALL_FEE;
                break;
            case 2: //解冻保养
                $bizType = MerchantBill::UNFREEZE_MAINTENANCE_FEE;
                break;
            case 3: //解冻清洗
                $bizType = MerchantBill::UNFREEZE_CLEANING_FEE;
                break;
        }


        $this->merchantAccountService->unfreeze($merchantId, $fee, $order, $bizType);

        Log::notice('解冻商家资金', ['msg' => '添加取消费用资金解冻记录成功', 'bizComment' => $bizComment, 'function' => __METHOD__]);

        return true;
    }


    /**
     * 后台取消其他费用处理商家资金解冻和流水
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
    public function cancelExtraFee(Order $order, $fee, $bizComment)
    {
        $merchantId = $order->merchant_id;
        // 解冻商家其他资金
        $bizType = MerchantBill::UNFREEZE_EXTRA_FEE;

        $this->merchantAccountService->unfreeze($merchantId, $fee, $order, $bizType);

        Log::info('解冻商家资金', ['msg' => '添加取消费用资金解冻记录成功', 'bizComment' => $bizComment, 'function' => __METHOD__]);

        return true;
    }

    /**
     * 后台取消配件费用处理商家资金解冻和流水
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
    public function cancelPartFee(Order $order, $fee, $bizComment)
    {
        $merchantId = $order->merchant_id;
        // 解冻商家其他资金
        $bizType = MerchantBill::UNFREEZE_PART_FEE;

        $this->merchantAccountService->unfreeze($merchantId, $fee, $order, $bizType);

        Log::info('解冻商家资金', ['msg' => '添加取消费用资金解冻记录成功', 'bizComment' => $bizComment, 'function' => __METHOD__]);

        return true;
    }

    /**
     * 后台取消佣金费用处理商家资金解冻和流水
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
    public function cancelMerchantBrokerageFee(Order $order, $fee, $bizComment)
    {
        $merchantId = $order->merchant_id;
        // 解冻商家其他资金
        $bizType = MerchantBill::UNFREEZE_MERCHANT_BROKERAGE;

        $this->merchantAccountService->unfreeze($merchantId, $fee, $order, $bizType);

        Log::info('解冻商家资金', ['msg' => '添加取消费用资金解冻记录成功', 'bizComment' => $bizComment, 'function' => __METHOD__]);

        return true;
    }


    /**
     * 后台取消费用效验order表费用
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
    public function updateFee($field, $fieldFee, $fee, $freezeFee, $price, $orderId, $installCnt)
    {
        if ($field == 'install_fee') {
            $oneFee = intval($fee / $installCnt); //安装费有数量
        } else {
            $oneFee = $fee; //维修送修没有数量
        }

        $data = [
            $field => DB::raw("{$field} - {$oneFee}"),
            'price' => DB::raw("price - {$fee}"),
        ];

        Log::info('$field='.$field.',$fieldFee='.$fieldFee.',$fee='.$fee.',$freezeFee='.$freezeFee.',$price='.$price.',$orderId='.$orderId,[__METHOD__]);
        $updated = $this->orderRepository->makeModel()
            ->where($field, $fieldFee)
            ->where('freeze_fee', $freezeFee)
            ->where('price', $price)
            ->where($field, '>=', $oneFee)
            ->where('id', $orderId)
            ->update($data);


        if (!$updated) {
            throw new OrderException(new MessageBag(['取消费用失败']));
        }

        return $updated;
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
    public function addParts(Request $request, $id, $partId = 0)
    {

        $data = $request->all();
        $partName = isset($data['part_name']) ? $data['part_name'] : '';
        $partFrom = isset($data['part_from']) ? $data['part_from'] : 0;
        $partDesc = isset($data['part_desc']) ? $data['part_desc'] : '';
        $expressName = isset($data['express_name']) ? $data['express_name'] : '';
        $expressNo = isset($data['express_no']) ? $data['express_no'] : '';
        $partPrice = isset($data['part_price']) ? $data['part_price'] : 0; //配件价格
        $isReturn = isset($data['is_return']) ? $data['is_return'] : 0; //支付方式
        $state = isset($data['state']) ? $data['state'] : OrderPart::END; //兼容老版本



        // TODO: Implement addParts() method.
        $order = $this->orderRepository->find($id, [
            'id',
            'merchant_id',
            'is_inspect',
            'inspect_fee',
            'order_type',
            'biz_type',
            'state',
            'user_name',
            'user_mobile',
            'full_address',
            'mode_status'
        ]);

        if ($order->state >= Order::VERIFY) {
            throw new OrderPartException(new MessageBag(['工单已完成，不能发送配件']));
        }

        //判断工单是否已经有商家提供的有效配件，以此判断是否需要自动追加上门费
        $isAddFeePartCut = $order->parts()->where('part_from',0)->where('parent_id',0)->whereIn('state',[-3,1,2,3,4,5])->count();

        $order->is_part = 1;
        $order->save();

        //修改还是创建
        if ($partId == 0) {
            $part['part_name'] = $partName;
            $part['part_desc'] = $partDesc;
            $part['parent_id'] = 0;
            $part['user'] = $order->user_name;
            $part['phone'] = $order->user_mobile;
            $part['address'] = $order->full_address;
            $part['part_from'] = $partFrom;
        }
        $part['state'] = $state;
        $part['part_price'] = $partPrice;
        $part['express_name'] = $expressName;
        $part['express_no'] = $expressNo;
        $part['mode_status'] = $order->mode_status;
        $part['is_return'] = $isReturn;

        if ($partId == 0) {
            $partAdded = $order->parts()->save(new OrderPart($part));
            if (!$partAdded) {
                throw new OrderPartException(new MessageBag(['发送配件失败']));
            }
            $partId = $partAdded->id;
        } else {
            $partAdded = $order->parts()->where('id', '=', $partId)->update($part);
        }


        //若返件添加返件信息
        if ($isReturn == 1) {
            $backPartName = isset($data['backpart_name']) ? $data['backpart_name'] : ''; //支付方式
            $backPartPhone = isset($data['backpart_phone']) ? $data['backpart_phone'] : ''; //支付方式
            $backPartProvince = isset($data['backpart_province']) ? $data['backpart_province'] : ''; //支付方式
            $backPartCity = isset($data['backpart_city']) ? $data['backpart_city'] : ''; //支付方式
            $backPartDistrict = isset($data['backpart_district']) ? $data['backpart_district'] : ''; //支付方式
            $backPartAddress = isset($data['backpart_address']) ? $data['backpart_address'] : ''; //支付方式
            $address = $backPartProvince . $backPartCity . $backPartDistrict . $backPartAddress;

            $backPart['part_name'] = $partName;
            $backPart['part_desc'] = $partDesc;
            $backPart['part_price'] = $partPrice;
            $backPart['part_from'] = $partFrom;
            $backPart['mode_status'] = $order->mode_status;
            $backPart['parent_id'] = $partId;
            $backPart['user'] = $backPartName;
            $backPart['phone'] = $backPartPhone;
            $backPart['address'] = $address;
            $res = $order->parts()->save(new OrderPart($backPart));

            if (!$res) {
                throw new OrderPartException(new MessageBag(['发送配件失败']));
            }
        }


        $partAdded = $order->parts()->find($partId);
        $orderType = intval($order->order_type);
        $bizType = intval($order->biz_type);


        //判断工单是否已经有商家提供的有效配件，以此判断是否需要自动追加上门费
        if ($isAddFeePartCut == 0 && 0 === $orderType && 1 === $bizType && $partAdded->part_from == 0 ) {
//            $this->addSystemInspectFee($id, config('luosidao.inspect_fee')); //系统追加，暂时不用了
            $this->addInspectFee($id, config('luosidao.inspect_fee'));
        }


        /** @var OrderPart $partAdded */

        return $partAdded;
    }

    /**
     * 追加拉修费用
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param        $id
     * @param        $distanceFee
     * @param string $extraDesc
     *
     * @return \App\Entities\OrderFee|false|\Illuminate\Database\Eloquent\Model
     * @throws \App\Exceptions\MerchantAccountException
     * @throws \App\Exceptions\MerchantBillException
     * @throws \App\Exceptions\OrderException
     * @throws \App\Exceptions\OrderFeeException
     */
    public function addDistanceFee($id, $distanceFee = 0, $extraDesc = '', $feeId = 0)
    {
        // TODO: Implement addExtraFee() method.
        $order = $this->orderRepository->find($id, [
            'id',
            'merchant_id',
            'price',
            'freeze_fee',
            'mode_status'
        ]);
        Log::notice($order, ['msg' => '追加其他费', 'f' => __METHOD__]);

        $distanceFee = yuanToFen($distanceFee);

        $price = intval($order->price);
        $freezeFee = intval($order->freeze_fee);
        $amount = $price + $distanceFee;
        $addFreezeFee = $this->checkFreezeFee($order, $amount, $freezeFee);

        $data = [
            'distance_fee' => DB::raw("distance_fee+{$distanceFee}"),
            'freeze_fee' => DB::raw("freeze_fee+{$addFreezeFee}"),
            'price' => DB::raw("price+{$distanceFee}"),
        ];


        $updated = $this->orderRepository->addDistanceFee($id, $data);

        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单其他费用失败']));
        }

        $orderFee = $this->addOrderFee($order, $distanceFee, 'distance_fee', $extraDesc, $feeId);

        return $orderFee;
    }

    /**
     * 追加远程费用
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param        $id
     * @param        $freightFee
     * @param string $extraDesc
     *
     * @return \App\Entities\OrderFee|false|\Illuminate\Database\Eloquent\Model
     * @throws \App\Exceptions\MerchantAccountException
     * @throws \App\Exceptions\MerchantBillException
     * @throws \App\Exceptions\OrderException
     * @throws \App\Exceptions\OrderFeeException
     */
    public function addFreightFee($id, $freightFee = 0, $extraDesc = '', $feeId = 0)
    {
        // TODO: Implement addExtraFee() method.
        $order = $this->orderRepository->find($id, [
            'id',
            'merchant_id',
            'price',
            'freeze_fee',
            'mode_status'
        ]);
        Log::notice($order, ['msg' => '追加其他费', 'f' => __METHOD__]);

        $freightFee = yuanToFen($freightFee);

        $price = intval($order->price);
        $freezeFee = intval($order->freeze_fee);
        $amount = $price + $freightFee;
        $addFreezeFee = $this->checkFreezeFee($order, $amount, $freezeFee);

        $data = [
            'freight_fee' => DB::raw("freight_fee+{$freightFee}"),
            'freeze_fee' => DB::raw("freeze_fee+{$addFreezeFee}"),
            'price' => DB::raw("price+{$freightFee}"),
        ];


        $updated = $this->orderRepository->addFreightFee($id, $data);

        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单其他费用失败']));
        }

        $orderFee = $this->addOrderFee($order, $freightFee, 'freight_fee', $extraDesc, $feeId);

        return $orderFee;
    }

    /**
     * 追加快递费用
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param        $id
     * @param        $expressFee
     * @param string $extraDesc
     *
     * @return \App\Entities\OrderFee|false|\Illuminate\Database\Eloquent\Model
     * @throws \App\Exceptions\MerchantAccountException
     * @throws \App\Exceptions\MerchantBillException
     * @throws \App\Exceptions\OrderException
     * @throws \App\Exceptions\OrderFeeException
     */
    public function addExpressFee($id, $expressFee = 0, $extraDesc = '', $feeId = 0)
    {
        // TODO: Implement addExtraFee() method.
        $order = $this->orderRepository->find($id, [
            'id',
            'merchant_id',
            'price',
            'freeze_fee',
            'mode_status'
        ]);
        Log::notice($order, ['msg' => '追加其他费', 'f' => __METHOD__]);

        $expressFee = yuanToFen($expressFee);

        $price = intval($order->price);
        $freezeFee = intval($order->freeze_fee);
        $amount = $price + $expressFee;
        $addFreezeFee = $this->checkFreezeFee($order, $amount, $freezeFee);

        $data = [
            'express_fee' => DB::raw("express_fee+{$expressFee}"),
            'freeze_fee' => DB::raw("freeze_fee+{$addFreezeFee}"),
            'price' => DB::raw("price+{$expressFee}"),
        ];


        $updated = $this->orderRepository->addExpressFee($id, $data);

        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单其他费用失败']));
        }

        $orderFee = $this->addOrderFee($order, $expressFee, 'express_fee', $extraDesc, $feeId);

        return $orderFee;
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
        $order = $this->orderRepository->find($id, [
            'id',
            'price',
            'freeze_fee',
            'merchant_id',
            'mode_status'
        ]);
        if ($partPrice > 0) {
            $partPrice = yuanToFen($partPrice);

            $price = intval($order->price);
            $freezeFee = intval($order->freeze_fee);
            $amount = $price + $partPrice;
            $addFreezeFee = $this->checkFreezeFee($order, $amount, $freezeFee);

            $data = [
                'part_fee' => DB::raw("part_fee+{$partPrice}"),
                'freeze_fee' => DB::raw("freeze_fee+{$addFreezeFee}"),
                'price' => DB::raw("price+{$partPrice}"),
            ];

            $updated = $this->orderRepository->addPart($id, $data);
            if (!$updated) {
                throw new OrderException(new MessageBag(['追加工单配件费用失败']));
            }

            // 商户资金账户处理
            $orderFee = $this->addOrderFee($order, $partPrice, 'part_fee', $partDesc, $feeId, $upOrDown, $partId);

            return $orderFee;
        }
    }

    public function addOrderFee(Order $order, $fee, $field, $extraDesc = '', $feeId = 0, $upOrDown = 'online', $partId = 0, $belongType = 1)
    {
        // TODO: Implement addOrderFee() method.
        Log::notice($order->toArray(), [__METHOD__]);
        $id = $order->id;
        $feeType = strtoupper($field);
        $feeTypeValue = constant(sprintf('%s::%s', OrderFee::class, $feeType));

        $orderFee = [
            'fee' => $fee,
            'fee_type' => $feeTypeValue,
            'fee_desc' => OrderFee::$feeTypes[$feeTypeValue],
            'state' => 1,
            'small_cat' => $order->small_cat ? $order->small_cat : '',
            'mode_status' => $order->mode_status ? $order->mode_status : 'merchant',
            'belong_type' => $belongType,
            'pay_type' => $upOrDown,
            'part_id' => $partId,
        ];
        if ('' !== $extraDesc) {
            $orderFee['extra_desc'] = $extraDesc;
        }

        if (0 != $feeId) {
            $orderFeeSaved = $order->orderFees()->where('id', '=', $feeId)->update($orderFee);
        } else {
            $orderFeeSaved = $order->orderFees()->save(new OrderFee($orderFee));
        }
        if (!$orderFeeSaved) {
            throw new OrderFeeException(new MessageBag(['添加费用明细记录失败']));
        }

        return $orderFeeSaved;
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
        // TODO: Implement addFixFee() method.
        $fixFee = yuanToFen($fixFee);
        /** @var Order $order */
        $order = $this->orderRepository->find($id, [
            'id',
            'merchant_id',
            'merchant_brokerage',
            'order_type',
            'is_part',
            'is_inspect',
            'inspect_fee',
            'is_fix',
            'state',
            'price',
            'freeze_fee',
            'mode_status',
        ]);

        $price = intval($order->price);
        $freezeFee = intval($order->freeze_fee);
        $amount = $price + $fixFee;
        $addFreezeFee = $this->checkFreezeFee($order, $amount, $freezeFee);

        $data = [
            'fix_fee' => DB::raw("fix_fee+{$fixFee}"),
            'price' => DB::raw("price+{$fixFee}"),
            'freeze_fee' => DB::raw("freeze_fee+{$addFreezeFee}"),
//            'small_cat' => $smallCat,
        ];

        $updated = $this->orderRepository->addFixFee($id, $data);
        $order->small_cat = $smallCat;
        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单维修费用失败']));
        }
        $feeType = strtoupper('fix_fee');
        $feeTypeValue = constant(sprintf('%s::%s', OrderFee::class, $feeType));

        $feeModel0 = $order->orderFees()->where('fee_type', $feeTypeValue)->where('state', 0)->first();
        $feeModel = $order->orderFees()->where('fee_type', $feeTypeValue)->where('state', 1)->first();
        if (count($feeModel)) {
            throw new OrderException(new MessageBag([ $feeModel->fee_desc . '只可以添加一条']));
        }elseif (count($feeModel0) && $feeId == 0 ) {
            throw new OrderException(new MessageBag([ $feeModel0->fee_desc . '只可以添加一条']));
        }

        $orderFee = $this->addOrderFee($order, $fixFee, 'fix_fee', $extraDesc, $feeId);


        return $orderFee;
    }

    /**
     * 追加安装费用
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param        $id
     * @param        $installFee
     * @param string $extraDesc
     * @param string $smallCat
     *
     * @return \App\Entities\OrderFee|false|\Illuminate\Database\Eloquent\Model
     * @throws \App\Exceptions\OrderException
     */
    public function addInstallFee($id, $installFee = 0, $extraDesc = '', $smallCat = '', $feeId = 0)
    {
        // TODO: Implement addFixFee() method.
        $installFee = yuanToFen($installFee);
        /** @var Order $order */
        $order = $this->orderRepository->find($id, [
            'id',
            'merchant_id',
            'merchant_brokerage',
            'order_type',
            'biz_type',
            'is_part',
            'is_inspect',
            'inspect_fee',
            'install_fee',
            'is_fix',
            'price',
            'freeze_fee',
            'mode_status',
            'install_cnt',
        ]);
        $bizType = intval($order->biz_type);
        $installCnt = intval($order->install_cnt);
        if($feeId != 0){
            $installFeeAll = $installFee;
            $installFee = $installFee / $installCnt;
        }else{
            $installFeeAll = $installFee * $installCnt;
        }

        $price = intval($order->price);
        $freezeFee = intval($order->freeze_fee);
        $amount = $price + $installFeeAll;
        $addFreezeFee = $this->checkFreezeFee($order, $amount, $freezeFee);

        $data = [
            'install_fee' => DB::raw("install_fee+{$installFee}"),
            'price' => DB::raw("price+{$installFeeAll}"),
            'freeze_fee' => DB::raw("freeze_fee+{$addFreezeFee}"),
//            'small_cat' => $smallCat,
        ];


        $updated = $this->orderRepository->addInstallFee($id, $data);
        $order->small_cat = $smallCat;
        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单费用失败']));
        }
        $field = 'install_fee';
        if ($bizType == 3) {
            $field = 'cleaning_fee';
        } elseif ($bizType == 2) {
            $field = 'maintenance_fee';
        }

        $feeType = strtoupper($field);
        $feeTypeValue = constant(sprintf('%s::%s', OrderFee::class, $feeType));


        $feeModel0 = $order->orderFees()->where('fee_type', $feeTypeValue)->where('state', 0)->first();
        $feeModel = $order->orderFees()->where('fee_type', $feeTypeValue)->where('state', 1)->first();
        if (count($feeModel)) {
            throw new OrderException(new MessageBag([ $feeModel->fee_desc . '只可以添加一条']));
        }elseif (count($feeModel0) && $feeId == 0 ) {
            throw new OrderException(new MessageBag([ $feeModel0->fee_desc . '只可以添加一条']));
        }

        $orderFee = $this->addOrderFee($order, $installFeeAll, $field, $extraDesc, $feeId);

        return $orderFee;
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
        // TODO: Implement addExtraFee() method.
        $order = $this->orderRepository->find($id, [
            'id',
            'merchant_id',
            'price',
            'freeze_fee',
            'mode_status'
        ]);
        Log::notice($order, ['msg' => '追加其他费', 'f' => __METHOD__]);

        $extraFee = yuanToFen($extraFee);

        $price = intval($order->price);
        $freezeFee = intval($order->freeze_fee);
        $amount = $price + $extraFee;
        $addFreezeFee = $this->checkFreezeFee($order, $amount, $freezeFee);

        $data = [
            'extra_fee' => DB::raw("extra_fee+{$extraFee}"),
            'freeze_fee' => DB::raw("freeze_fee+{$addFreezeFee}"),
            'price' => DB::raw("price+{$extraFee}"),
        ];


        $updated = $this->orderRepository->addExtraFee($id, $data);

        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单其他费用失败']));
        }

        $orderFee = $this->addOrderFee($order, $extraFee, 'extra_fee', $extraDesc, $feeId);

        return $orderFee;
    }

    public function addInspectFee($id, $inspectFee = 0, $extraDesc = '', $feeId = 0)
    {

        $inspectFee = yuanToFen($inspectFee);

        // TODO: Implement addInspectFee() method.
        /** @var Order $order */
        $order = $this->orderRepository->find($id, [
            'id',
            'merchant_id',
            'merchant_brokerage',
            'order_type',
            'small_cat',
            'state',
            'price',
            'freeze_fee',
            'mode_status'
        ]);

        Log::notice($order, ['msg' => '追加检测费', 'f' => __METHOD__]);
        $price = intval($order->price);
        $freezeFee = intval($order->freeze_fee);
        $amount = $price + $inspectFee;
        $addFreezeFee = $this->checkFreezeFee($order, $amount, $freezeFee);

        $data = [
            'inspect_fee' => DB::raw("inspect_fee+{$inspectFee}"),
            'price' => DB::raw("price+{$inspectFee}"),
            'freeze_fee' => DB::raw("freeze_fee+{$addFreezeFee}"),
        ];


        $updated = $this->orderRepository->addInspectFee($id, $data);

        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单检测费用失败']));
        }
        $orderFee = $this->addOrderFee($order, $inspectFee, 'inspect_fee', $extraDesc, $feeId);

        return $orderFee;
    }


    public function addSystemInspectFee($id, $inspectFee = 0, $extraDesc = '', $feeId = 0)
    {

        $inspectFee = yuanToFen($inspectFee);

        // TODO: Implement addInspectFee() method.
        /** @var Order $order */
        $order = $this->orderRepository->find($id, [
            'id',
            'merchant_id',
            'merchant_brokerage',
            'order_type',
            'small_cat',
            'state',
            'price',
            'freeze_fee',
            'mode_status'
        ]);

        Log::notice($order, ['msg' => '追加检测费', 'f' => __METHOD__]);
        $price = intval($order->price);
        $freezeFee = intval($order->freeze_fee);
        $amount = $price + $inspectFee;
        $addFreezeFee = $this->checkFreezeFee($order, $amount, $freezeFee);

        $data = [
            'inspect_fee' => DB::raw("inspect_fee+{$inspectFee}"),
            'price' => DB::raw("price+{$inspectFee}"),
            'freeze_fee' => DB::raw("freeze_fee+{$addFreezeFee}"),
        ];


        $updated = $this->orderRepository->addInspectFee($id, $data);

        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单检测费用失败']));
        }
        $orderFee = $this->addOrderFee($order, $inspectFee, 'system_inspect_fee', $extraDesc, $feeId);

        return $orderFee;
    }



    public function removeInspectFee($id, $inspectFee = 0)
    {
        // TODO: Implement removeInspectFee() method.
        /** @var Order $order */
        $order = $this->orderRepository->find($id, [
            'id',
            'merchant_id',
            'merchant_brokerage',
            'order_type',
            'mode_status',
            'small_cat'
        ]);

        $data = [
            'inspect_fee' => 0,
            'price' => DB::raw("price-{$inspectFee}"),
        ];


        $updated = $this->orderRepository->removeInspectFee($id, $data, $inspectFee);

        if (!$updated) {
            throw new OrderException(new MessageBag(['取消工单检测费用失败']));
        }


        $order = $this->orderRepository->find($id, [
            'id',
            'merchant_id',
            'merchant_brokerage',
            'order_type',
            'is_part',
            'is_inspect',
            'inspect_fee',
            'small_cat',
            'is_fix',
            'mode_status',
        ]);

        return $order;
    }


    /**
     * 工单列表-工单详情
     * @author shaozeming@xiongmaojinfu.com
     */
    public function orderInfo($id, $state)
    {
        if (!$id) {
            throw new OrderException(new MessageBag([config('error.2000')]), 2000);
        }
        $fields = [
            'id',
            'order_no',
            'order_desc',
            'fix_result',
            'inspect_result',
            'is_inspect',
            'is_fix',
            'is_urgent',
            'is_part',
            'big_cat',
            'middle_cat',
            'middle_cat_id',
            'small_cat',
            'full_address',
            'merchant_name',
            'merchant_tel',
            'merchant_id',
            'user_mobile',
            'user_name',
            'user_lng',
            'user_lat',
            'booked_at',
            'price',
            'install_fee',
            'install_cnt',
            'inspect_fee',
            'fix_fee',
            'worker_fee',
            'part_fee',
            'extra_fee',
            'urgent_fee',
            'verify_code',
            'published_at',
            'accepted_at',
            'confirmed_at', //商家确认付款时间
            'finished_at',
            'inspected_at',   //检测完成时间
            'biz_type',
            'order_type',
            'visiting_at',
            'merchant_brokerage',
            'state',
            'worker_id',
            'worker_name',
            'worker_mobile',
            'merchant_logo',
            'verify_code',
            'product_id',
            'canceled_at',
            'cancel_reason',
            'cancel_fee',
            'dealer_id',
            'distance_fee',
            'freight_fee',
            'express_fee',
            'freeze_fee',
        ];
        $order = $this->orderRepository
            ->with([
                'parts' => function ($query) {
                    return $query
                        ->where('state', '!=', -2)
                        ->where('parent_id', 0)
                        ->orderBy('id', 'desc');
                },
                'comment' => function ($query) {
                    return $query->where('commentable_id', '=', getMerchantId());
                },
                'orderFees' => function ($query) {
                    //保外单费用清单处理
                    return $query
                        ->where('state', '!=', -2)
                        ->orderBy('id', 'desc');
                },
                'images' => function ($query) {
                    return $query->select(['relation_path', 'uploadable_id', 'is_inspect']);
                }
            ])->find($id, $fields);


        $order->worker_logo = '';
        if ($order->worker_id) {
            $worker = $order->worker()->first();
            if (isset($worker->logo)) {
                $order->worker_logo = $worker->logo;
            }
        }
        foreach ($order->parts as $part) {
            $part->transform();
        };
        foreach ($order->orderFees as $fees) {
            $fees->transform();
        };

        $orders = $order->transform();


        $feeFiled = [
            'fix_fee' => '维修费',
            'part_fee' => '配件费',
            'inspect_fee' => '上门费',
            'extra_fee' => '其他费',
            'urgent_fee' => '加急费',
            'distance_fee' => '远程费',
            'freight_fee' => '拉修费',
            'express_fee' => '快递费',
            'merchant_brokerage' => '佣金',
        ];


        $feeList[] = ['fee_txt' => '费用总额', 'fee' => ($orders['price'] + $orders['merchant_brokerage']) . '元'];
        if (in_array($orders['biz_type'], [0, 2, 3])) {
            $feeList[] = ['fee_txt' => '单个' . $orders['biz_type_txt'] . '费', 'fee' => $orders['install_fee'] . '元'];
            $feeList[] = ['fee_txt' => $orders['biz_type_txt'] . '数量', 'fee' => $orders['install_cnt'] . '个'];
        }
        foreach ($feeFiled as $key => $value) {
            if (isset($orders[$key]) && $orders[$key] > 0) {
//                if($key == 'fix_fee' && $orders['small_cat']){
//                    $value = $value.'('.$orders['small_cat'].')';
//                }
                $feeList[] = ['fee_txt' => $value, 'fee' => $orders[$key] . '元'];
            }
        }
        $orders['fee_list'] = $feeList;

        return $orders;
    }




    //验证商家保证金是否不低于设置
    public function checkEnsureFee($mid=0){

        if(!$mid){
            $mid = getMerchantId();
        }
        $merchant =  Merchant::find($mid);
        if(count($merchant)){
            $account = $merchant->account()->first();
            $available = $account->available;
            $ensure_price = $account->ensure_price;
            if($available < $ensure_price){
                throw new BaseException(new MessageBag(['账户可用余额低于系统保障金：'.fenToYuan($ensure_price).'元，请及时充值']),9001);
            }
        }else{
            throw new \Exception('商家不存在');
        }
    }
}
