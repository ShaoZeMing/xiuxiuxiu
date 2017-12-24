<?php
namespace App\Services;

use App\Exceptions\OrderException;
use App\Repositories\MerchantUserRepository;
use App\Repositories\OrderFeeRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\MessageBag;

/**
 *  OrderService.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: OrderService.php 2017-03-21 下午4:57 $
 */
class OrderManagerService
{
    private $bizTypeParams = [
        '0' => 'Install',      //安装
        '1' => 'Fix',          //维修
        '2' => 'Maintenance',  //保养
        '3' => 'Cleaning',     //清洗
        '4' => 'Torepair',     //送修
    ];

    private $orderTypeParams = [
        '0' => 'InService',
        '1' => 'OutService',
    ];

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
    }

    public function getService($bizType, $orderType, $bizTypeParams = [], $orderTypeParams = [])
    {
        if ($bizTypeParams) {
            $this->bizTypeParams = $bizTypeParams;
        }
        if ($orderTypeParams) {
            $this->orderTypeParams = $orderTypeParams;
        }
        if (!isset($this->bizTypeParams[$bizType]) || !isset($this->orderTypeParams[$orderType])) {
            throw new OrderException(new MessageBag(['工单服务不存在']));
        }
        $serviceName = 'Order' . $this->bizTypeParams[$bizType] . $this->orderTypeParams[$orderType];
        $fileName = app_path() . '/Services/Orders/' . $serviceName . '.php';
        if (!file_exists($fileName)) {
            throw new OrderException(new MessageBag(['工单服务不存在']));
        }

        $className = "App\\Services\\Orders\\" . $serviceName;
        $obj = $className::getInstanceService(
            $this->orderRepository,
            $this->merchantAccountService,
            $this->userRepository,
            $this->workerAccountService,
            $this->orderFeeRepository,
            $this->merchantUserRepository,
            $this->platformAccountService,
            $this->luosidaoAccountService
        );
        return $obj;
    }
}
