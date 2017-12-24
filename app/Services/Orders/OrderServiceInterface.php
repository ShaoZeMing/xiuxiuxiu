<?php
namespace App\Services\Orders;

use App\Entities\Order;
use Illuminate\Http\Request;

/**
 *  OrderService.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: OrderService.php 2017-03-21 下午4:57 $
 */
interface OrderServiceInterface
{
    /**
     * 创建工单
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data);

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
    public function update($id, $date);

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
    public function publish($id);

    //添加费用明细
    public function addOrderFee(Order $order, $fee, $field, $extraDesc = '', $feeId = 0);

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
     * @throws \App\Exceptions\PlatformBillException
     * @throws \App\Exceptions\WorkerAccountException
     * @throws \App\Exceptions\WorkerBillException
     */
    public function editMerchantOrderNum($id);

    //确认工单
    public function confirm($id);

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
    public function reassign($id, $workerId = 0, $workerName = '', $workerMobil = '');

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
    public function cancel($id, $cancelReason = '', $cancelableId = '', $cancelableType = '');

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
    public function getCancelFee($id);

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

    public function addFixFee($id, $fixFee = 0, $extraDesc = '', $smallCat = '', $feeId = 0);

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

    public function addParts(Request $request,$id, $partId = 0);

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
    public function addPartFee($id, $partPrice = 0, $partDesc = '', $feeId = 0);

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
    public function addExtraFee($id, $extraFee = 0, $extraDesc = '', $feeId = 0);

    public function addInspectFee($id, $inspectFee = 0);

    public function removeInspectFee($id, $inspectFee = 0);

    public function findByOrderId($id, $columns = ['*']);

    /**
     * 拨打电话
     *
     * @param array $data
     *
     * @return array
     * @throws \App\Exceptions\OrderException
     * @internal param \App\Entities\Order|array $order
     *
     */
    public function call($id);

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
    public function updating(array $data);

    /**
     * 监听工单创建前的事件。
     *
     * @param  array $data
     *
     * @return array
     * @throws \App\Exceptions\OrderException
     */
    public function creating(array $data);

    /**
     * 检查商家资金账户
     *
     * @param  array $data
     *
     * @return array
     * @throws \App\Exceptions\OrderException
     */
    public function checkMerchantAccount(Order $order, $amount = 0);

    /**
     * 修改工单费用明细
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param \App\Entities\Order $order
     * @param                     $feeId
     * @param                     $state
     *
     */
    public function updateOrderFee(Order $order, $feeId, $state);

    /**
     * 取消费用
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param \App\Entities\Order $order
     * @param                     $feeId
     * @param                     $state
     *
     */
    public function cancelFixFee(Order $order, $fee, $bizComment);
}
