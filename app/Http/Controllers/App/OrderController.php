<?php

namespace App\Http\Controllers\App;

use App\Entities\Order;
use App\Events\WeixinMsgEvent;
use App\Http\Controllers\Controller;
use App\Repositories\CompanyCustomerRepository;
use App\Repositories\CompanyProductRepository;
use App\Repositories\CompanyServiceTechnicalsRepository;
use App\Repositories\CompanySiteRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\LogsRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderStarRepository;
use App\Repositories\OrderThirdServicesRepository;
use App\Repositories\ThirdServicesRepository;
use App\Repositories\UserRepository;
use App\Validators\OrderValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    public $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * 创建工单
     *
     * @SWG\Post(path="/api/v1/order/create",
     *   tags={"api1.1"},
     *   summary="创建工单-可测试-zj",
     *   description="创建工单",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="customer_id",
     *     type="string",
     *     description="客户id",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="customer_name",
     *     type="string",
     *     description="客户名字",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="customer_mobile",
     *     type="string",
     *     description="客户手机号",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="order_type",
     *     type="string",
     *     description="保内0， 保外1",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="product_id",
     *     type="string",
     *     description="产品id",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="product_name",
     *     type="string",
     *     description="产品name",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="buy_time",
     *     type="string",
     *     description="购买时间",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="service_content_id",
     *     type="string",
     *     description="服务内容id",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="service_content_name",
     *     type="string",
     *     description="服务内容",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="service_mode_id",
     *     type="string",
     *     description="服务模式",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="service_mode_name",
     *     type="string",
     *     description="服务模式名称",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="malfunction_id",
     *     type="string",
     *     description="故障id",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="malfunction_name",
     *     type="string",
     *     description="故障名称",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="level_id",
     *     type="string",
     *     description="优先级id",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="level_name",
     *     type="string",
     *     description="优先级名称",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="connection_name",
     *     type="string",
     *     description="联系人",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="connection_mobile",
     *     type="string",
     *     description="联系人手机号",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="order_desc",
     *     type="string",
     *     description="维修说明",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Response(response="default",      description="操作成功")
     * )
     */
    public function create(Request $request, CompanyCustomerRepository $companyCustomerRepository, CompanyProductRepository $companyProductRepository, CustomerRepository $customerRepository)
    {
        try {
            $data = $request->all();
            Log::notice('发单', [$data, __METHOD__]);
            $validator = OrderValidator::verifyCreateData($data);
            if ($validator !== true) {
                throw new \Exception($validator);
            }
            $user = $this->getUser();
            $data['create_user_id'] = $user->id;
            $data['customer_id'] = hashidsDecode($request->get('customer_id'));
            $data['customer_name'] = $request->get('customer_name');
            $data['customer_mobile'] = $request->get('customer_mobile');
            $data['product_id'] = hashidsDecode($request->get('product_id'));
            $data['product_name'] = $request->get('product_name');
            $data['buy_time'] = $request->get('buy_time');
            $data['service_content_id'] = hashidsDecode($request->get('service_content_id'));
            $data['service_content_name'] = $request->get('service_content_name');
            $data['mode_id'] = hashidsDecode($request->get('service_mode_id'));
            $data['mode_name'] = $request->get('service_mode_name');
            $data['malfunction_id'] = hashidsDecode($request->get('malfunction_id'));
            $data['malfunction_name'] = $request->get('malfunction_name');
            $data['level_id'] = hashidsDecode($request->get('level_id'));
            $data['level_name'] = $request->get('level_name');
            $data['connect_user_name'] = $request->get('connection_name');
            $data['connect_user_mobile'] = $request->get('connection_mobile');
            $data['order_no'] = app('sequence')->generateOrderNo();
            $data['order_type'] = $request->get('order_type');
            $data['order_desc'] = $request->get('order_desc');
            $data['company_id'] = $user->company_id;

            DB::beginTransaction();
            app('orderService')->createOrder($data, $user);
            DB::commit();
            return $this->response(0, config('error.0'));
        } catch (\Exception $ex) {
            Log::error($ex);
            DB::rollback();
            $msg = config('error.2048');
            return $this->response(2048, $msg);
        }
    }

    /**
     * 查看工单列表
     *
     * @SWG\Get(path="/api/v1/orders/lists",
     *   tags={"api1.1"},
     *   summary="查看工单列表-可测试-zj",
     *   description="查看工单列表",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="query",
     *     name="type",
     *     type="string",
     *     description="首页 index, ",
     *     default="1",
     *     required=false
     *   ),
     * @SWG\Parameter(
     *     in="query",
     *     name="page",
     *     type="string",
     *     description="页码",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Response(response="default",     description="操作成功")
     * )
     */
    public function lists(Request $request)
    {
        try {
            $user = $this->getUser();
            $type = $request->get('type');
            $page = $request->get('page', 1);
            $orderWhere = getOrderListWhere($user, $request);
            $output = app('orderService')->orderList($user, $orderWhere, $type);
            $output['page'] = $page;
            Log::notice('得到工单列表', $output);
            return $this->response(0, config('error.0'), $output);
        } catch (\Exception $ex) {
            Log::error($ex);
            return $this->response(0, config('error.0'));
        }
    }


    /**
     * 查看工单详情
     *
     * @SWG\Get(path="/api/v1/order/{id}",
     *   tags={"api1.1"},
     *   summary="查看工单详情-可测试-zj",
     *   description="查看工单详情 parts_back 中state 是0 表示没有填写过反件信息， 1表示师傅填写过反件信息等待确认， 2表示受理人收到反件信息 parts : is_part_worker_receive 0 表示没有收到， 1表示师傅收到 ",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbHNkLWFwaS5mZW5neGlhb2JhaS5jbi9hdXRoL2xvZ2luIiwiaWF0IjoxNDk1MDAwMjIxLCJleHAiOjE1MzEyODgyMjEsIm5iZiI6MTQ5NTAwMDIyMSwianRpIjoiNXNkZXlVa2t0TWZoa050VyIsInN1YiI6NTE4NDIxODk1MTQ2NjM2MjkyfQ.h4l2QZpJitwbIh63yh_ef_P7tXnm1R4XUgm8rnDv9Zg"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     default="ND",
     *     required=true
     *   ),
     * @SWG\Response(response="default",   description="操作成功")
     * )
     */
    public function show($id, Request $request, OrderStarRepository $orderStarRepository)
    {
        $context = [
            'id' => $id,
            'method' => __METHOD__,
            'msg' => '获取工单详细信息',
        ];
        try {
            $orderId = hashidsDecode($id);
            $userId = $this->getUserId();
            $order = app('orderService')->getOrderDetail($orderId, $userId);
            $order = $order->transform();
            $order['is_stars'] = 0;
            if (isset($order['stars']) && count($order['stars'])) {
                $order['is_stars'] = 1;
            }

            if ($order['state'] != Order::CONFIRMED) {
                unset($order['comments']);
            }

            $order['images_exists'] = 0;
            if (isset($order['images']) && count($order['images'])) {
                $order['images_exists'] = 1;
            }
            unset($order['stars']);
            $key = config('redis.user_read_order') . $id;
            if (!Redis::hGet($key, $userId)) {
                Redis::hSet($key, $userId, 1);
            }
            Log::info('工单详情', $context);
            return $this->response(0, config('error.0'), $order);
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            return $this->response(9999, config('error.9999'));
        }
    }

    /**
     * 取消工单
     *
     * @SWG\Post(path="/api/v1/order/{id}/cancel",
     *   tags={"api1.1"},
     *   summary="取消工单-可测试-zj",
     *   description="取消工单",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="cancel_context",
     *     type="string",
     *     description="取消原因",
     *     default="太远了",
     *     required=true
     *   ),
     * @SWG\Response(response="default",           description="操作成功")
     * )
     */
    public function cancel($id, Request $request)
    {
        $orderId = hashidsDecode($id);
        $context = [
            'id' => $id,
            'order_id' => $orderId,
            'msg' => '取消工单',
            'method' => __METHOD__,
        ];
        Log::info('取消工单', $context);
        try {
            $user = $this->getUser();
            app('orderService')->cancelOrder($user, $orderId, $request);
            return $this->response(0, config('error.0'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            $msg = $ex->getMessage() ? $ex->getMessage() : config('error.2016');
            return $this->response(2016, $msg);
        }
    }


    /**
     * 完成工单
     *
     * @SWG\Post(path="/api/v1/order/{id}/confirm",
     *   tags={"api1.1"},
     *   summary="完成工单-可测试-zj",
     *   description="完成工单",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="handle_txt",
     *     type="string",
     *     description="处理说明",
     *     default="修好了",
     *     required=true
     *   ),
     * @SWG\Response(response="default",            description="操作成功")
     * )
     */
    public function confirm($id, Request $request)
    {
        $orderId = hashidsDecode($id);
        $context = [
            'id' => $id,
            'order_id' => $orderId,
            'msg' => '完成工单',
            'method' => __METHOD__,
        ];
        Log::info('完成工单', $context);
        try {
            $operDesc = $request->get('handle_txt');
            if (!trim($operDesc)) {
                return $this->response(2017, config('error.2017'));
            }
            $order = $this->orderRepository->find($orderId);
            $userId = $this->getUserId();

            if ($order->state == Order::CONFIRMED) {
                return $this->response(2074, config('error.2074'));
            }
            if ($order->state < Order::INSERVICE || $order->state == Order::CANCELED) {
                return $this->response(2017, config('error.2017'));
            }
            $unConfirmFee = $order->fees()->where(['state' => 0])->count();
            if ($unConfirmFee) {
                return $this->response(2066, config('error.2066'));
            }
            DB::enableQueryLog();
            $unConfirmPart = $order->parts()->where(['state' => 0])->count();
            $unConfirmReceivePart = $order->parts()->where(['state' => 1, 'is_part_worker_receive' => 0])->count();
            Log::notice('完成工单', DB::getQueryLog());
            if ($unConfirmPart || $unConfirmReceivePart) {
                return $this->response(2068, config('error.2068'));
            }
            $data = [
                'state' => Order::CONFIRMED,
                'oper_desc' => $operDesc,
            ];
            $flag = $this->orderRepository->update($data, $orderId);
            if ($flag) {
                try {
                    $url = rtrim(config('saas.comment_url'), '/') . '/' . $id;
                    $dwzUrl = tinyurl($url);
                    $data = [
                        $order->order_no,
                        $order->service_content_name,
                        $dwzUrl
                    ];
                    $type = 'confirm_order';
                    Log::info('完成工单发送短信,url:' . $dwzUrl, $context);
                    app('sms')->sendSms($order->customer_name, $order->customer_mobile, $type, $data);
                    $data = [
                        $order->order_no,
                        $order->worker_name,
                    ];
                    app('logModel')->insertLog($order, $userId, $type, $data);
                    event(new WeixinMsgEvent($order));
                    return $this->response(0, config('error.0'));
                } catch (\Exception $ex) {
                    Log::error($ex);
                }
            }
            return $this->response(2018, config('error.2018'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            return $this->response(2018, config('error.2018'));
        }
    }

    /**
     * 删除工单
     *
     * @SWG\Get(path="/api/v1/order/{id}/del",
     *   tags={"api1.1"},
     *   summary="删除工单-可测试-zj",
     *   description="删除工单",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     default="8llA6o79NVvG",
     *     required=true
     *   ),
     * @SWG\Response(response="default",       description="操作成功")
     * )
     */
    public function del($id, Request $request)
    {
        $orderId = hashidsDecode($id);
        $context = [
            'id' => $id,
            'order_id' => $orderId,
            'msg' => '删除工单',
            'method' => __METHOD__,
        ];
        Log::info('删除工单', $context);
        try {
            app('orderService')->delOrder($orderId);
            return $this->response(0, config('error.0'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            $msg = $ex->getMessage() ? $ex->getMessage() : config('error.2019');
            return $this->response(2019, $msg);
        }
    }

    /**
     * 得到星标工单
     *
     * @SWG\Get(path="/api/v1/orders/starlists",
     *   tags={"api1.1"},
     *   summary="得到星标工单-zj",
     *   description="得到星标工单",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="query",
     *     name="page",
     *     type="string",
     *     description="页码",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Response(response="default",         description="操作成功")
     * )
     */
    public function getStarLists(Request $request, OrderStarRepository $orderStarRepository)
    {
        try {
            $context = [
                'msg' => '星标列表',
                'data' => $request->all(),
                'method' => __METHOD__,
            ];
            $user = $this->getUser();
            $where = [
                'user_id' => $user->id,
                'company_id' => $user->company_id,
            ];
            $page = $request->get('page', 1);
            $limit = config('repository.paginate.limit');
            $columns = [
                'id',
                'order_no',
                'state',
                'created_at',
                'cat_name',
                'product_name',
                'service_content_name',
                'technicals_mobile',
                'technicals_name',
            ];

            $stars = $orderStarRepository->with(
                [
                    'order' => function ($query) use ($columns) {
                        return $query->select($columns);
                    }]
            )->scopeQuery(
                function ($query) use ($where) {
                    return $query->select(['id', 'order_id'])->where($where)->orderBy('id', 'DESC');
                }
            )->paginate($limit);
            $stars->each(
                function (&$star) {
                    $order = $star->order->transform();
                    $star->order_no = $order['order_no'];
                    $star->state = $order['state'];
                    $star->state_txt = $order['state_txt'];
                    $star->cat_name = $order['cat_name'];
                    $star->product_name = $order['product_name'];
                    $star->service_content_name = $order['service_content_name'];
                    $star->technicals_mobile = $order['technicals_mobile'];
                    $star->technicals_name = $order['technicals_name'];
                    $star->created_at = $order['created_at'];
                    $star->transform();
                    unset($star['order']);
                    return $star;
                }
            );
            $stars = $stars->toArray();
            $stars = isset($stars['data']) ? $stars['data'] : [];
            Log::info('工单星标列表', [$stars, $context]);
            $output = [
                'list' => $stars,
                'page' => $page
            ];
            return $this->response(0, config('error.0'), $output);
        } catch (\Exception $ex) {
            Log::error($ex);
            return $this->response(0, config('error.0'));
        }
    }

    /**
     * 标记星标工单
     *
     * @SWG\Get(path="/api/v1/order/{id}/star",
     *   tags={"api1.1"},
     *   summary="标记星标工单-zj",
     *   description="标记星标工单",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     required=true
     *   ),
     * @SWG\Response(response="default",        description="操作成功")
     * )
     */
    public function setStarOrder($id, Request $request, OrderStarRepository $orderStarRepository)
    {

        try {
            $orderId = hashidsDecode($id) ? hashidsDecode($id) : $id;
            $user = $this->getUser();
            $data = [
                'order_id' => $orderId,
                'user_id' => $user->id,
                'company_id' => $user->company_id,
            ];
            $orderStarRepository->firstOrCreate($data, $data);
            return $this->response(0, config('error.0'));
        } catch (\Exception $ex) {
            Log::error($ex);
            return $this->response(2061, config('error.2061'));
        }
    }

    /**
     * 删除标记星标工单
     *
     * @SWG\Get(path="/api/v1/order/{id}/delstar",
     *   tags={"api1.1"},
     *   summary="删除标记星标工单-zj",
     *   description="删除标记星标工单",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     required=true,
     *     default="anzOdAgONNdP"
     *   ),
     * @SWG\Response(response="default",           description="操作成功")
     * )
     */
    public function delStarOrder($id, Request $request, OrderStarRepository $orderStarRepository)
    {
        try {
            $id = hashidsDecode($id) ? hashidsDecode($id) : $id;
            $user = $this->getUser();
            $where = [
                'order_id' => $id,
                'user_id' => $user->id,
            ];
            $orderStarRepository->deleteWhere($where);
            return $this->response(0, config('error.0'));
        } catch (\Exception $ex) {
            Log::error($ex);
            return $this->response(2061, config('error.2061'));
        }
    }

    /**
     * 预约时间
     *
     * @SWG\Post(path="/api/v1/order/{id}/booking",
     *   tags={"api1.1"},
     *   summary="预约时间-可测试-zj",
     *   description="预约时间",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbHNkLWFwaS5mZW5neGlhb2JhaS5jbi9hdXRoL2xvZ2luIiwiaWF0IjoxNDk1MDAwMjIxLCJleHAiOjE1MzEyODgyMjEsIm5iZiI6MTQ5NTAwMDIyMSwianRpIjoiNXNkZXlVa2t0TWZoa050VyIsInN1YiI6NTE4NDIxODk1MTQ2NjM2MjkyfQ.h4l2QZpJitwbIh63yh_ef_P7tXnm1R4XUgm8rnDv9Zg"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     default="8llA6o79NVvG",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="booked_at",
     *     type="string",
     *     description="工单ID",
     *     default="8llA6o79NVvG",
     *     required=true
     *   ),
     * @SWG\Response(response="default",            description="操作成功")
     * )
     */
    public function booking($id, Request $request)
    {
        $orderId = hashidsDecode($id);
        $context = [
            'id' => $id,
            'order_id' => $orderId,
            'data' => $request->all(),
            'msg' => '预约工单',
            'method' => __METHOD__,
        ];
        Log::info('预约工单', $context);
        try {
            $bookedAt = $request->get('booked_at');
            $userId = $this->getUserId();
            if (!$bookedAt) {
                return $this->response(2024, config('error.2024'));
            }
            $order = $this->orderRepository->find($orderId);
            if ($order->state == Order::BOOKED) {
                return $this->response(2021, config('error.2021'));
            }
            if ($order->state > 20 || $order->state == Order::DELETED) {
                return $this->response(2022, config('error.2022'));
            }

            $data = [
                'state' => Order::BOOKED,
                'booked_at' => $bookedAt,
            ];
            DB::enableQueryLog();

            $flag = $this->orderRepository->update($data, $orderId);
            Log::info('预约工单', [DB::getQueryLog()]);
            if ($flag) {
                try {
                    $smsData = [
                        $order->order_no,
                        $order->service_content_name,
                        $bookedAt,
                        $order->worker_name,
                        $order->worker_mobile,
                    ];
                    $type = 'booked_at';
                    app('sms')->sendSms($order->customer_name, $order->customer_mobile, $type, $smsData);

                    $data = [
                        $order->order_no,
                        $order->worker_name,
                        $order->customer_name,
                        $bookedAt,
                    ];
                    app('logModel')->insertLog($order, $userId, $type, $data);
                } catch (\Exception $ex) {
                    Log::error($ex);
                }
                //预约工单发送微信推送
                Log::info('预约后工单给微信下单用户推送',[__METHOD__]);
                event(new WeixinMsgEvent($order));
                return $this->response(0, config('error.0'));
            }
            return $this->response(2023, config('error.2023'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            return $this->response(2023, config('error.2023'));
        }

    }

    /**
     * 受理工单
     *
     * @SWG\Post(path="/api/v1/order/{id}/handle",
     *   tags={"api1.1"},
     *   summary="受理工单-可测试-zj",
     *   description="受理工单",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     default="8llA6o79NVvG",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="technicals_id",
     *     type="string",
     *     description="技术支持的id",
     *     default="8llA6o79NVvG",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="state",
     *     type="string",
     *     description="是否受理工单 0 不受理， 1受理",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="refuse_handle_context",
     *     type="string",
     *     description="不受理工单原因",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Response(response="default",           description="操作成功")
     * )
     */
    public function handle($id, Request $request, CompanyServiceTechnicalsRepository $companyServiceTechnicalsRepository)
    {
        $orderId = hashidsDecode($id);
        $context = [
            'id' => $id,
            'order_id' => $orderId,
            'data' => $request->all(),
            'msg' => '受理工单',
            'method' => __METHOD__,
        ];
        Log::info('受理工单', $context);
        try {
            $order = $this->orderRepository->find($orderId);
            $user = $this->getUser();
            app('orderService')->handleOrder($order, $user, $request, $companyServiceTechnicalsRepository);

            return $this->response(0, config('error.0'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            $msg = $ex->getMessage() ? $ex->getMessage() : config('error.2029');
            return $this->response(2029, $msg);
        }
    }

    /**
     * 分配工单
     *
     * @SWG\Post(path="/api/v1/order/{id}/dispatch",
     *   tags={"api1.1"},
     *   summary="分配工单-可测试-zj",
     *   description="分配工单",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     default="8llA6o79NVvG",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="site_id",
     *     type="string",
     *     description="网点id",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Response(response="default",             description="操作成功")
     * )
     */
    public function dispatchSite($id, Request $request, CompanySiteRepository $companySiteRepository)
    {
        $orderId = hashidsDecode($id);
        $context = [
            'id' => $id,
            'order_id' => $orderId,
            'data' => $request->all(),
            'msg' => '分配工单',
            'method' => __METHOD__,
        ];
        Log::info('分配工单', $context);
        try {
            $order = $this->orderRepository->find($orderId);
            $user = $this->getUser();
            app('orderService')->dispatchOrderToSite($orderId, $request, $user, $companySiteRepository);
            return $this->response(0, config('error.0'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            return $this->response(2040, config('error.2040'));
        }
    }

    /**
     * 指派工单
     *
     * @SWG\Post(path="/api/v1/order/{id}/assign",
     *   tags={"api1.1"},
     *   summary="指派工单-可测试-zj",
     *   description="指派工单",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     default="8llA6o79NVvG",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="user_id",
     *     type="string",
     *     description="师傅id",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Response(response="default",           description="操作成功")
     * )
     */
    public function assign($id, Request $request, UserRepository $userRepository)
    {
        $orderId = hashidsDecode($id);
        $context = [
            'id' => $id,
            'order_id' => $orderId,
            'msg' => '指派工单',
            'method' => __METHOD__,
        ];
        Log::info('指派工单', $context);
        try {
            $user = $this->getUser();
            app('orderService')->assignOrder($orderId, $user, $request, $userRepository);
            return $this->response(0, config('error.0'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            $msg = $ex->getMessage() ? $ex->getMessage() : config('error.2041');
            return $this->response(2041, $msg);
        }
    }

    /**
     * 工单开始服务
     *
     * @SWG\Get(path="/api/v1/order/{id}/start",
     *   tags={"api1.1"},
     *   summary="工单开始服务-可测试-zj",
     *   description="工单开始服务",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     default="8llA6o79NVvG",
     *     required=true
     *   ),
     * @SWG\Response(response="default",         description="操作成功")
     * )
     */
    public function startService($id, Request $request)
    {
        $orderId = hashidsDecode($id);
        $context = [
            'id' => $id,
            'order_id' => $orderId,
            'msg' => '工单开始服务',
            'method' => __METHOD__,
        ];
        Log::info('工单开始服务', $context);
        try {
            $order = $this->orderRepository->find($orderId);
            $userId = $this->getUserId();
            if ($order->state == Order::INSERVICE) {
                return $this->response(2031, config('error.2031'));
            }
            if ($order->state != Order::BOOKED) {
                return $this->response(2032, config('error.2032'));
            }

            $data = [
                'state' => Order::INSERVICE,
            ];
            $flag = $this->orderRepository->update($data, $orderId);
            if ($flag) {
                $data = [
                    $order->order_no,
                    $order->worker_name,
                ];
                app('logModel')->insertLog($order, $userId, 'start_service', $data);

                return $this->response(0, config('error.0'));
            }
            return $this->response(2033, config('error.2033'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            return $this->response(2033, config('error.2033'));
        }
    }

    /**
     * 编辑工单
     *
     * @SWG\Get(path="/api/v1/order/{id}/edit",
     *   tags={"api1.1"},
     *   summary="编辑工单-可测试-zj",
     *   description="编辑工单",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单id",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="customer_id",
     *     type="string",
     *     description="客户id",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="customer_name",
     *     type="string",
     *     description="客户名字",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="customer_mobile",
     *     type="string",
     *     description="客户手机号",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="order_type",
     *     type="string",
     *     description="保内0， 保外1",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="product_id",
     *     type="string",
     *     description="产品id",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="product_name",
     *     type="string",
     *     description="产品name",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="buy_time",
     *     type="string",
     *     description="购买时间",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="service_content_id",
     *     type="string",
     *     description="服务内容id",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="service_content_name",
     *     type="string",
     *     description="服务内容",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="service_mode_id",
     *     type="string",
     *     description="服务模式",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="service_mode_name",
     *     type="string",
     *     description="服务模式名称",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="malfunction_id",
     *     type="string",
     *     description="故障id",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="malfunction_name",
     *     type="string",
     *     description="故障名称",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="level_id",
     *     type="string",
     *     description="优先级id",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="level_name",
     *     type="string",
     *     description="优先级名称",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="connection_name",
     *     type="string",
     *     description="联系人",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="connection_mobile",
     *     type="string",
     *     description="联系人手机号",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="order_desc",
     *     type="string",
     *     description="维修说明",
     *     default="2",
     *     required=true
     *   ),
     * @SWG\Response(response="default",        description="操作成功")
     * )
     */
    public function edit($id, Request $request, CompanyCustomerRepository $companyCustomerRepository, CompanyProductRepository $companyProductRepository, CustomerRepository $customerRepository)
    {
        try {
            if ($request->method() == 'POST') {
                $data = $request->all();
                $validator = OrderValidator::verifyCreateData($data);
                if ($validator !== true) {
                    throw new \Exception($validator);
                }
                $orderId = hashidsDecode($id);
                $order = $this->orderRepository->find($orderId);
                $user = $this->getUser();
                $data['customer_id'] = hashidsDecode($request->get('customer_id'));
                $data['customer_name'] = $request->get('customer_name');
                $data['customer_mobile'] = $request->get('customer_mobile');
                $data['product_id'] = hashidsDecode($request->get('product_id'));
                $data['product_name'] = $request->get('product_name');
                $data['buy_time'] = $request->get('buy_time');
                $data['service_content_id'] = hashidsDecode($request->get('service_content_id'));
                $data['service_content_name'] = $request->get('service_content_name');
                $data['mode_id'] = hashidsDecode($request->get('service_mode_id'));
                $data['mode_name'] = $request->get('service_mode_name');
                $data['malfunction_id'] = hashidsDecode($request->get('malfunction_id'));
                $data['malfunction_name'] = $request->get('malfunction_name');
                $data['level_id'] = hashidsDecode($request->get('level_id'));
                $data['level_name'] = $request->get('level_name');
                $data['connect_user_name'] = $request->get('connection_name');
                $data['connection_user_mobile'] = $request->get('connection_mobile');
                $data['order_no'] = app('sequence')->generateOrderNo();
                $data['order_type'] = $request->get('order_type');
                $data['order_desc'] = $request->get('order_desc');

                DB::beginTransaction();
                app('orderService')->editOrder($orderId, $data, $user);
                DB::commit();
                return $this->response(0, config('error.0'));
            } else {
                $cloumns = [
                    'id',
                    'customer_id',
                    'customer_name',
                    'customer_mobile',
                    'product_id',
                    'product_name',
                    'buy_time',
                    'service_content_id',
                    'service_content_name',
                    'mode_id',
                    'mode_name',
                    'malfunction_id',
                    'malfunction_name',
                    'level_id',
                    'level_name',
                    'connect_user_name',
                    'connect_user_mobile',
                    'order_no',
                    'order_type',
                    'order_desc',
                    'cat_id',
                ];
                $orderId = hashidsDecode($id);
                $order = $this->orderRepository->find($orderId, $cloumns);
                return $this->response(0, config('error.0'), $order->transform());
            }
        } catch (\Exception $ex) {
            Log::error($ex);
            DB::rollback();
            $msg = config('error.2047');
            return $this->response(2047, $msg);
        }
    }

    /**
     * 工单分配给第三方
     *
     * @SWG\Post(path="/api/v1/order/{id}/dispatch-third",
     *   tags={"api1.1"},
     *   summary="工单分配给第三方-可测试-zj",
     *   description="工单分配给第三方",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     default="8llA6o79NVvG",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="content",
     *     type="string",
     *     description="第三方id,单号;第三方id,单号;",
     *     default="1",
     *     required=true
     *   ),
     * @SWG\Response(response="default",                   description="操作成功")
     * )
     */
    public function dispatchThirdService($id, Request $request, ThirdServicesRepository $thirdServicesRepository, OrderThirdServicesRepository $orderThirdServicesRepository)
    {
        $orderId = hashidsDecode($id);
        $context = [
            'id' => $id,
            'order_id' => $orderId,
            'msg' => '分配工单',
            'method' => __METHOD__,
        ];
        Log::info('分配工单', $context);
        try {
            $user = $this->getUser();
            DB::beginTransaction();
            app('orderService')->dispatchOrderToThird($orderId, $request, $user, $thirdServicesRepository, $orderThirdServicesRepository);
            DB::commit();
            return $this->response(0, config('error.0'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            DB::rollback();
            $msg = $ex->getMessage() ? $ex->getMessage() : config('error.2040');
            return $this->response(2040, $msg);
        }
    }

    /**
     * 工单的第三方信息列表 | 设置第三方状态
     *
     * @SWG\Get(path="/api/v1/order/{id}/third",
     *   tags={"api1.1"},
     *   summary="工单的第三方信息 设置第三方状态-可测试-zj",
     *   description="get工单的第三方信息， post 设置第三方状态  get 表示返回列表",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     default="8llA6o79NVvG",
     *     required=true
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="third_id",
     *     type="string",
     *     description="第三方id post",
     *     default="1",
     *     required=false
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="state",
     *     type="string",
     *     description="state 1有师傅接单 -1拒单",
     *     default="1",
     *     required=false
     *   ),
     * @SWG\Response(response="default",         description="操作成功")
     * )
     */
    public function thirdService($id, Request $request, OrderThirdServicesRepository $orderThirdServicesRepository)
    {
        $context = [
            'order_id' => hashidsDecode($id),
            'id' => $id,
            'data' => $request->all(),
            'msg' => '第三方服务',
            'method' => __METHOD__,
        ];
        $orderId = hashidsDecode($id) ? hashidsDecode($id) : $id;

        try {
            Log::info('第三方服务', $context);
            if ($request->method() == 'POST') {
                $this->setThirdSerivceState($orderId, $request, $orderThirdServicesRepository);
                return $this->response(0, config('error.0'));
            } else {
                $output = $this->getThirdServices($request, $orderId);
                return $this->response(0, config('error.0'), $output);
            }
        } catch (\Exception $ex) {
            Log::error($ex);
            return $this->response(2053, config('error.2053'));
        }
    }

    protected function setThirdSerivceState($orderId, $request, $orderThirdServicesRepository)
    {
        try {
            $thirdId = hashidsDecode($request->get('third_id')) ? hashidsDecode($request->get('third_id')) : $request->get('third_id');
            $state = $request->get('state');
            DB::beginTransaction();
            $thirdInfo = $orderThirdServicesRepository->find($thirdId);
            if ($thirdInfo->state != 0) {
                throw new Exception(config('error.2053'));
            }
            $flag = $orderThirdServicesRepository->update(['state' => $state], $thirdId);
            $orderUpdateData = [
                'state' => Order::ACCEPTED,
            ];
            $flagOrder = $this->orderRepository->update($orderUpdateData, $orderId);
            if ($flag && $flagOrder) {
                DB::commit();
                return true;
            }
            throw new \Exception(config('error.2053'));
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error($ex);
            throw new \Exception(config('error.2053'));
        }
    }

    protected function getThirdServices($request, $orderId)
    {
        try {
            $limit = config('repository.paginate.limit');
            $page = $request->get('page', 1);
            $order = $this->orderRepository->find($orderId);
            $thirdServices = $order->thirdServices()->select(['id', 'order_id', 'third_order_no', 'third_service_name', 'state'])->paginate($limit);
            $thirdServices->each(
                function ($service) {
                    return $service->transform();
                }
            );
            if ($thirdServices) {
                $thirdServices = $thirdServices->toArray();
            }
            $thirdServices = isset($thirdServices['data']) ? $thirdServices['data'] : [];
            $output = [
                'list' => $thirdServices,
                'page' => $page,
            ];
            return $output;
        } catch (\Exception $ex) {
            Log::error($ex);
            throw new \Exception(config('error.2053'));
        }
    }

    /**
     * 查看工单进度
     *
     * @SWG\Get(path="/api/v1/order/{id}/processing",
     *   tags={"api1.1"},
     *   summary="查看工单进度-可测试-zj",
     *   description="查看工单进度",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="header",
     *     name="Authorization",
     *     type="string",
     *     description="用户旧的jwt-token, value以Bearer开头",
     *     required=true,
     *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vc2FzLmZlbmd4aWFvYmFpLmNuL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwNTkwMzUsImV4cCI6MTU0MDM0NzAzNSwibmJmIjoxNTA0MDU5MDM1LCJqdGkiOiJMOGU1eTFxc1JtVXpGbTlxIiwic3ViIjoxfQ.7xqdsXh7Iwz_FbX63EwaqB2fRXuPoCt_aFfhrsr0I60"
     *   ),
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="string",
     *     description="工单ID",
     *     default="8llA6o79NVvG",
     *     required=true
     *   ),
     * @SWG\Response(response="default",              description="操作成功")
     * )
     */
    public function processing($id, LogsRepository $logsRepository)
    {
        $content = [
            'msg' => '查看进度',
            'order_id' => $id,
            'method' => __METHOD__,
        ];
        try {
            $orderId = hashidsDecode($id);
            $logsRepository->setPresenter("Prettus\\Repository\\Presenter\\ModelFractalPresenter");

            Log::info('查看进度', [$orderId, $content]);
            $results = $logsRepository->scopeQuery(
                function ($query) use ($orderId) {
                    return $query->select(['id', 'title', 'content', 'created_at'])
                        ->where(['billable_id' => $orderId, 'billable_type' => 'App\\Entities\\Order'])
                        ->orderBy('id', 'asc');
                }
            )->all();
            $results = isset($results['data']) ? $results['data'] : [];
            $output = ['list' => $results];
            return $this->response(0, config('error.0'), $output);
        } catch (\Exception $ex) {
            Log::error($ex);
            return $this->response(0, config('error.0'));
        }
    }
}
