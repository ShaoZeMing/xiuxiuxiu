<?php
namespace App\Services\Orders;


/**
 * Interface OrderServiceInterface
 * @package App\Services\Orders
 */
interface OrderServiceInterface
{

    /**
     * 创建工单
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param array $data
     * @return mixed
     */
    public function create(array $data);


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $id
     * @param $date
     * @return mixed
     * 更新工单
     */
    public function update($id, $date);


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $id
     * @return mixed
     * 确认工单
     */
    public function confirm($id);


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $id
     * @return mixed
     * 取消工单
     */
    public function cancel($id);


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param array $data
     * @return mixed
     * 更新前动作
     */
    public function updating(array $data);


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param array $data
     * @return mixed
     * 创建前动作
     */
    public function creating(array $data);


}
