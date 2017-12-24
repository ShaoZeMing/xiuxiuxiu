<?php
namespace App\Services\Orders;

use App\Entities\LuosidaoBill;
use App\Entities\Merchant;
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
class OrderBaseOutService extends OrderBaseService
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

        $orderFeeData = [
            'state' => '-2',
        ];
        if ($state >= Order::PUBLISHED) {
            $feeSaved = $order->orderFees()->update($orderFeeData);
            if (!$feeSaved) {
                throw new OrderException(new MessageBag(['费用删除失败']));
            }
        }

        if ($workerId) {
            event(new OrderCanceledEvent($id));
        }

        // 保外单到此就结束处理了
        return true;
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
            'order_type',
            'biz_type',
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
        $price = $order->orderFees()->whereNotIn('fee_type', [OrderFee::PLATFORM_REWARDS, OrderFee::MERCHANT_BROKERAGE])
            ->where('order_id', hashidsDecode($id))
            ->where('state', 1)
            ->where('pay_type', 'online')
            ->where('belong_type', '0')//费用结算给商家
            ->sum('fee');
        $BrokerageAmount = $fixFee + ($installFee * $installCnt) + $inspectFee;
        //计算商家佣金
        $merchantBrokerage = OrderBillingService::merchantBrokerage($BrokerageAmount, $orderType, $merchantId);
        $data = [
            'fee_all' => fenToYuan($price),
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
            'state',
            'inspect_fee',
            'fix_fee',
            'part_fee',
            'install_fee',
            'install_cnt',
            'worker_id',
            'merchant_brokerage',
            'worker_brokerage',
            'worker_fee',
            'freeze_fee',
            'order_type',
            'merchant_id',
            'extra_fee',
            'pay_state'
        ];

        $order = $this->orderRepository->find($id, $fields);
        Log::notice('保外结单 start', [$order, __METHOD__]);

        //支付状态已经成功， 如果状态不是60 回写60; 如果是 直接返回
        if ($order->pay_state == 1 && $order->worker_fee) {
            if ($order->state != 60) {
                $orderUpdated = $this->orderRepository->confirm($id, []);
                if (!$orderUpdated) {
                    Log::error('保外结单 结束', [$order, __METHOD__]);
                    throw new OrderException(new MessageBag(['确认订单失败']));
                }
            }
            $order = $this->orderRepository->find($id);
            Log::notice('保外结单 end', [$order, __METHOD__]);
            return $order;
        }

        $parts = $order->parts()->where('parent_id',0)->whereIn('state', [0, 2, 3, 4, 5])->count();
        if ($parts) {
            throw new OrderException(new MessageBag(['申请配件未被商家确认']));
        }
        $orderType = intval($order->order_type);
        $workerId = intval($order->worker_id);
        $merchantId = intval($order->merchant_id);
        $fixFee = intval($order->fix_fee);
        $inspectFee = intval($order->inspect_fee);
        $installFee = intval($order->install_fee);
        $installCnt = intval($order->install_cnt);
        $price = intval($order->price);
        $extraFee = intval($order->extra_fee);
        $merchantBrokerage = intval($order->merchant_brokerage);

        //计算工单费用列表线上支付确认费用总额
        $totalAmount = $this->orderFeeRepository->getTotalAmount($id, 0);

        Log::info('工单费用表线上支付费用总额:' . $totalAmount, ['msg' => '费用总额', 'price' => $price, 'f' => __METHOD__]);
        if ($totalAmount - $merchantBrokerage != $price) {
            Log::error('费用有误', ['$totalAmount' => $totalAmount, '$merchantBrokerage' => $merchantBrokerage, '$price' => $price]);
            throw new OrderException(new MessageBag(['费用有误，请核对后重试']));
        }
        //需要收取师傅佣金总费用
        $workerBrokerageAmount = $fixFee + ($installFee * $installCnt) + $inspectFee + $extraFee;
        //保外支付给商家的费用
        $merchantBrokerageAmount = $this->orderFeeRepository->getTotalAmount($id, $orderType); //商家获得总费用
        //计算收取师傅佣金
        $workerBrokerage = OrderBillingService::workerBrokerage($workerBrokerageAmount, $orderType);
        $workerFee = ($price + $merchantBrokerage) - $workerBrokerage - $merchantBrokerageAmount;

        Log::notice([
            'id' => $id,
            'msg' => '保外工单结算费用核对',
            '线上总费用' => $totalAmount,
            '支付给商家总费用' => $merchantBrokerageAmount,
            '需要收取师傅佣金总费用' => $workerBrokerageAmount,
            '师傅最后获得' => $workerFee,
            'order price' => $price,
            '平台奖励' => $merchantBrokerage,
        ], [__METHOD__]);
        if ($workerFee <= 0) {
            throw new OrderException(new MessageBag(['师傅获取费用有误，请核对后重试']));
        }
        $payload['worker_brokerage'] = $workerBrokerage;
        $payload['worker_fee'] = $workerFee;
        $payload['merchant_fee'] = $merchantBrokerageAmount;

        //判断保外是否支付了费用
        $is_pay = $order->orderPays()
            ->where('order_pays.state', 1)
            ->count();
        Log::info('是否支付费用', ['is_pay' => $is_pay, 'id' => $id, 'f' => __METHOD__]);

        $payload['pay_state'] = $is_pay ? 1 : 0;  //修改支付状态

        //确认工单
        $orderUpdated = $this->orderRepository->confirm($id, $payload);
        if (!$orderUpdated) {
            throw new OrderException(new MessageBag(['确认订单失败']));
        }

        //判断保外是否支付了费用
        if ($is_pay) {
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
                    //平台收入
                    if ($workerBrokerage > 0) {
                        $bizType = LuosidaoBill::WORKER_ORDER_BROKERAGE;
                        $luosidaoAccountService->increment($luosidaoAccountId, $workerBrokerage, $order, $bizType);
                    }
                    //支付商家保外工单奖励
                    if ($merchantBrokerage > 0) {
                        $bizType = LuosidaoBill::REWARDS_TO_MERCHANT;
                        $luosidaoAccountService->decrement($luosidaoAccountId, $merchantBrokerage, $order, $bizType);
                    }
                },
                $this->retrySleep
            );
            // 商户资金账户处理
            // 解冻商家资金，冻结的费用里包括检测费，检测费需要配件的情况下才会收取
            $merchantAccountService = $this->merchantAccountService;

            // 保外工单是工单收入费用
            $payoutFee = $merchantBrokerageAmount;
            if ($payoutFee > 0) {
                retry(
                    $this->retryTimes,
                    function () use ($merchantAccountService, $merchantId, $payoutFee, $order) {
                        return $merchantAccountService->orderIncrement($merchantId, $payoutFee, $order);
                    },
                    $this->retrySleep
                );
            }
        }

        $this->editMerchantOrderNum($id, 'confirm');

        $order = $this->orderRepository->find($id);
        Log::notice('保外结单 end', [$order, __METHOD__]);

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
        $context = [
            'order_id' => $id,
            'method' => __METHOD__,
            'msg' => '发布工单',
        ];
        $fields = [
            'id',
            'order_no',
            'price',
            'merchant_id',
            'merchant_name',
            'merchant_tel',
            'merchant_logo',
            'product_id',
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
            'inspect_fee',
            'urgent_fee',
            'merchant_brokerage',
            'worker_brokerage',
            'biz_type',
            'install_fee',
            'install_cnt',
            'small_cat',
            'mode_status',
        ];
        $order = $this->orderRepository->find($id, $fields);
        Log::info('查看创建order信息', $order->toArray());
        $price = $order->price;
        $brokerage = $order->merchant_brokerage;
        $orderType = intval($order->order_type);
        $bizType = intval($order->biz_type);
        // 检测费+维修费+平台佣金
        $amount = $price + $brokerage;

        $updated = $this->orderRepository->publish($id, $amount, $orderType);
        if (!$updated) {
            Log::error($context, [$amount]);
            throw new OrderException(new MessageBag(['工单发布失败']));
        }

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
                if ($bizType == 3 && $field === 'install_fee') {
                    $field = 'cleaning_fee';
                } elseif ($bizType == 2 && $field === 'install_fee') {
                    $field = 'maintenance_fee';
                }


                $this->addOrderFee($order, $fee, $field, '', 0, 'online', 0, 1);
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
     * 审核发布工单
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
        $context = [
            'order_id' => $id,
            'method' => __METHOD__,
            'msg' => '发布工单',
        ];
        $fields = [
            'id',
            'order_no',
            'price',
            'merchant_id',
            'merchant_name',
            'merchant_tel',
            'merchant_logo',
            'product_id',
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
            'inspect_fee',
            'urgent_fee',
            'merchant_brokerage',
            'worker_brokerage',
            'biz_type',
            'install_fee',
            'install_cnt',
            'small_cat',
            'mode_status',
        ];
        $order = $this->orderRepository->find($id, $fields);
        Log::info('查看创建order信息', $order->toArray());
        $price = $order->price;
        $brokerage = $order->merchant_brokerage;
        $orderType = intval($order->order_type);
        $bizType = intval($order->biz_type);
        // 检测费+维修费+平台佣金
        $amount = $price + $brokerage;

        $updated = $this->orderRepository->savePublish($id, $amount, $orderType);
        if (!$updated) {
            Log::error($context, [$amount]);
            throw new OrderException(new MessageBag(['工单发布失败']));
        }

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
                if ($bizType == 3 && $field === 'install_fee') {
                    $field = 'cleaning_fee';
                } elseif ($bizType == 2 && $field === 'install_fee') {
                    $field = 'maintenance_fee';
                }


                $this->addOrderFee($order, $fee, $field, '', 0, 'online', 0, 1);
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
        $upOrDown = isset($data['pay_type']) ? $data['pay_type'] : 'online'; //支付方式
//        $isReturn = isset($data['is_return']) ? $data['is_return'] : 0; //支付方式
        $isReturn =  0; //保外没有返件

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
            throw new OrderPartException(new MessageBag(['工单已完成，不能追加配件了']));
        }

        $order->is_part = 1;
        $order->save();

        if ($partId == 0) {
            $part['part_name'] = $partName;
            $part['part_desc'] = $partDesc;
            $part['parent_id'] = 0;
            $part['user'] = $order->user_name;
            $part['phone'] = $order->user_mobile;
            $part['address'] = $order->full_address;
            $part['part_from'] = $partFrom;
            $part['part_price'] = $partPrice;
        }
        $part['state'] = OrderPart::END;
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
            if (!$partAdded) {
                throw new OrderPartException(new MessageBag(['发送配件失败']));
            }
        }


        if ($isReturn == 1) {
            $backPartName = isset($data['backpart_name']) ? $data['backpart_name'] : ''; //支付方式
            $backPartPhone = isset($data['backpart_phone']) ? $data['backpart_phone'] : ''; //支付方式
            $backPartProvince = isset($data['backpart_province']) ? $data['backpart_province'] : ''; //支付方式
            $backPartCity = isset($data['backpart_city']) ? $data['backpart_city'] : ''; //支付方式
            $backPartDistrict = isset($data['backpart_district']) ? $data['backpart_district'] : ''; //支付方式
            $backPartAddress = isset($data['backpart_address']) ? $data['backpart_address'] : ''; //支付方式
            $address = $backPartProvince . $backPartCity . $backPartDistrict . $backPartAddress;

            $part['part_name'] = $partName;
            $part['part_desc'] = $partDesc;
            $part['part_price'] = $partPrice;
            $part['part_from'] = $partFrom;
            $part['mode_status'] = $order->mode_status;
            $part['parent_id'] = $partId;
            $part['user'] = $backPartName;
            $part['phone'] = $backPartPhone;
            $part['address'] = $address;
            $res = $order->parts()->save(new OrderPart($part));
            if (!$res) {
                throw new OrderPartException(new MessageBag(['发送配件失败']));
            }
        }

        $partAdded = $order->parts()->find($partId);

        Log::info('保外配件追加',[$partAdded->toArray(),__METHOD__]);
        $partFrom = intval($partFrom);
        if ($partFrom == 0) {
            $this->addPartFee($id, $partPrice, $partDesc, 0, $upOrDown, $partId);
        }

        /** @var OrderPart $partAdded */

        return $partAdded;
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
            'merchant_id',
            'mode_status'
        ]);

        if ($partPrice > 0) {
            $partPrice = yuanToFen($partPrice);
            if (!in_array($upOrDown, ['online', 'offline'])) {
                throw new OrderException(new MessageBag(['费用支付方式非online或offline']));
            }
            //线上交易费用周
            if ($upOrDown == 'online') {
                $data = [
                    'part_fee' => DB::raw("part_fee+{$partPrice}"),
                    'price' => DB::raw("price+{$partPrice}"),
                ];

                $updated = $this->orderRepository->addPart($id, $data);
                if (!$updated) {
                    throw new OrderException(new MessageBag(['追加工单配件费用失败']));
                }
            }
            // 商户资金账户处理
            $orderFee = $this->addOrderFee($order, $partPrice, 'part_fee', $partDesc, $feeId, $upOrDown, $partId);

            return $orderFee;
        }
    }



    //重写追费
    //保外追费不能冻结商家的钱（待处理）
    public function addOrderFee(Order $order, $fee, $field, $extraDesc = '', $feeId = 0, $upOrDown = 'online', $partId = 0, $belongType = 0)
    {
        // TODO: Implement addOrderFee() method.
        Log::info($order->toArray(), [__METHOD__]);
        $feeType = strtoupper($field);
        $feeTypeValue = constant(sprintf('%s::%s', OrderFee::class, $feeType));

        $orderFee = [
            'fee' => $fee,
            'fee_type' => $feeTypeValue,
            'fee_desc' => OrderFee::$feeTypes[$feeTypeValue],
            'state' => 1,
            'small_cat' => $order->small_cat ? $order->small_cat : '',
            'mode_status' => $order->mode_status,
            'pay_type' => $upOrDown, //线上或线下
            'fee_from' => 0,  //相当于平台添加，师傅不可删除
            'belong_type' => $belongType, //结算给谁
            'part_id' => $partId,
        ];
        if ('' !== $extraDesc) {
            $orderFee['extra_desc'] = $extraDesc;
        }

        if (0 !== $feeId) {
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
            'mode_status',
        ]);

        $data = [
            'fix_fee' => DB::raw("fix_fee+{$fixFee}"),
            'price' => DB::raw("price+{$fixFee}"),
//            'small_cat' => $smallCat,
        ];

        $updated = $this->orderRepository->addFixFee($id, $data);

        $order->small_cat = $smallCat;
        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单维修费用失败']));
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
            'biz_type',
            'order_type',
            'is_part',
            'is_inspect',
            'inspect_fee',
            'install_fee',
            'is_fix',
            'mode_status',
        ]);
        $bizType = $order->biz_type;

        $data = [
            'install_fee' => DB::raw("install_fee+{$installFee}"),
            'price' => DB::raw("price+{$installFee}"),
//            'small_cat' => $smallCat,
        ];

        $updated = $this->orderRepository->addInstallFee($id, $data);
        $order->small_cat = $smallCat;
        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单安装费用失败']));
        }
        $field = 'install_fee';
        if ($bizType == 3) {
            $field = 'cleaning_fee';
        } elseif ($bizType == 2) {
            $field = 'maintenance_fee';
        }
        $orderFee = $this->addOrderFee($order, $installFee, $field, $extraDesc, $feeId);

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
            'mode_status'
        ]);

        $extraFee = yuanToFen($extraFee);

        $this->checkMerchantAccount($order, $extraFee);

        $data = [
            'extra_fee' => DB::raw("extra_fee+{$extraFee}"),
            'freeze_fee' => DB::raw("freeze_fee+{$extraFee}"),
            'price' => DB::raw("price+{$extraFee}"),
        ];


        $updated = $this->orderRepository->addExtraFee($id, $data);

        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单其他费用失败']));
        }

        $orderFee = $this->addOrderFee($order, $extraFee, 'extra_fee', $extraDesc, $feeId);

        return $orderFee;
    }

    public function addInspectFee($id, $inspectFee = 0)
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
            'mode_status'
        ]);

        $data = [
            'inspect_fee' => DB::raw("inspect_fee+{$inspectFee}"),
            'price' => DB::raw("price+{$inspectFee}"),
            'mode_status' => $order->mode_status,
        ];


        $merchantBrokerage = $order->getMerchantBrokerage($inspectFee, $order->merchant_id);
        $data['merchant_brokerage'] = DB::raw("merchant_brokerage+{$merchantBrokerage}");


        $freezeFee = $inspectFee + $merchantBrokerage;

        $this->checkMerchantAccount($order, $freezeFee);

        $data['freeze_fee'] = DB::raw("freeze_fee+{$freezeFee}");


        $updated = $this->orderRepository->addInspectFee($id, $data);

        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单检测费用失败']));
        }

        $orderFee = $this->addOrderFee($order, $inspectFee, 'inspect_fee');
        if ($merchantBrokerage > 0) {
            $this->addOrderFee($order, $merchantBrokerage, 'merchant_brokerage');
        }

        return $orderFee;
    }

  public function addSystemInspectFee($id, $inspectFee = 0)
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
            'mode_status'
        ]);

        $data = [
            'inspect_fee' => DB::raw("inspect_fee+{$inspectFee}"),
            'price' => DB::raw("price+{$inspectFee}"),
            'mode_status' => $order->mode_status,
        ];


        $merchantBrokerage = $order->getMerchantBrokerage($inspectFee, $order->merchant_id);
        $data['merchant_brokerage'] = DB::raw("merchant_brokerage+{$merchantBrokerage}");


        $freezeFee = $inspectFee + $merchantBrokerage;

        $this->checkMerchantAccount($order, $freezeFee);

        $data['freeze_fee'] = DB::raw("freeze_fee+{$freezeFee}");


        $updated = $this->orderRepository->addInspectFee($id, $data);

        if (!$updated) {
            throw new OrderException(new MessageBag(['追加工单检测费用失败']));
        }

        $orderFee = $this->addOrderFee($order, $inspectFee, 'system_inspect_fee');
        if ($merchantBrokerage > 0) {
            $this->addOrderFee($order, $merchantBrokerage, 'merchant_brokerage');
        }

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

////        $merchantBrokerage = $order->getMerchantBrokerage($inspectFee);
        $merchantBrokerage = intval($order->merchant_brokerage);
////        $freezeFee = $inspectFee + $merchantBrokerage;

        $data = [
            'inspect_fee' => 0,
            'price' => DB::raw("price-{$inspectFee}"),
            'merchant_brokerage' => DB::raw("merchant_brokerage-{$merchantBrokerage}"),
        ];


        $updated = $this->orderRepository->removeInspectFee($id, $data, $inspectFee);

        if (!$updated) {
            throw new OrderException(new MessageBag(['取消工单检测费用失败']));
        }

        if ($order->orderFees()) {
            $order->orderFees()->where('fee_type', OrderFee::INSPECT_FEE)->delete();
            $order->orderFees()->where('fee_type', OrderFee::MERCHANT_BROKERAGE)->delete();
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
                        ->orderBy('id', 'desc');                },
                'comment' => function ($query) {
                    return $query->where('commentable_id', '=', getMerchantId());
                },
                'orderFees' => function ($query) use ($state) {
                    //保外单费用清单处理
                    if ($state >= Order::INSERVICE) {

                        if (isDealer()) {
                            return $query
                                ->where('fee_type', '!=', OrderFee::PLATFORM_REWARDS)
                                ->where('state', '!=', -2)
                                ->where('belong_type', '0')//费用结算给商家
                                ->orderBy('id', 'desc');
                        } else {
                            return $query
                                ->where('state', '!=', -2)
                                ->where('belong_type', '0')//费用结算给商家
                                ->orderBy('id', 'desc');
                        }
                    }
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

        if (isDealer() && $order->state >= Order::INSERVICE) {
            $order->merchant_brokerage = 0;
        }
        $order->small_cat = '';
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
            'merchant_brokerage' => '平台奖励',
        ];

        $feeList[] = ['fee_txt' => '费用总额', 'fee' => ($orders['price'] + $orders['merchant_brokerage']) . '元'];
        if (in_array($orders['biz_type'], [0, 2, 3]) && $orders['install_fee']>0) {
            $feeList[] = ['fee_txt' => '单个' . $orders['biz_type_txt'] . '费', 'fee' => $orders['install_fee'] . '元'];
            $feeList[] = ['fee_txt' => $orders['biz_type_txt'] . '数量', 'fee' => $orders['install_cnt'] . '个'];
        }
        foreach ($feeFiled as $key => $value) {
            if (isset($orders[$key]) && $orders[$key] > 0) {
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
