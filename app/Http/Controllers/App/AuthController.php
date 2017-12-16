<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\AppUpgradeRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserAppRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\CompanyRepository;
use App\Repositories\CompanySiteRepository;
use JWTAuth;
use Infrastructure\Push\PushManager;

class AuthController extends Controller
{
    protected $_sms_type = [
          'forget',
          'repwd'
    ];

    /**
     * 检查手机号是否已经注册
     *
     * @author zhangjun@xiaobaiyoupin.com
     *
     * @param mixed Request $request
     *
     * @return mixed
     */
    public function checkMobileExists($mobile, $userRepository)
    {
        $context = [
                'mobile' => $mobile,
                'method' => __METHOD__,
            ];
        Log::info(config('error.0'), $context);
        $where = ['mobile' => $mobile];
        $user  = $userRepository->findWhere($where)->first();
        if (count($user)) {
            Log::notice('手机号已经注册', $context);
            return $user;
        }
        return false;
    }
    /**
     * 校验手机号是否注册
     *
     * @SWG\Post(path="/api/v1/auth/mobile",
     *   tags={"api-auth"},
     *   summary="校验手机号是否注册-可测试-zj",
     *   description="校验手机号是否注册",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="formData",
     *     name="mobile",
     *     type="string",
     *     description="手机号",
     *     required=true,
     *   ),
     * @SWG\Response(response="default",     description="操作成功")
     * )
     */
    public function mobileVerify(Request $request, UserRepository $userRepository)
    {
        $context = [
            'data' => $request->all(),
            'method' => __METHOD__,
            'msg'    => '验证手机号',
        ];
        try {
            $mobile = $request->get('mobile');
            $flag   = isMobile($mobile);
            if (!$flag) {
                Log::error('手机号不正确', $context);
                return $this->response('1002', config('error.1002'));
            }
            $info = $this->checkMobileExists($mobile, $userRepository);
            if ($info) {
                if($info->state == 1) {
                    return $this->response(0, config('error.0'));
                } else {
                    Log::error('账户已经被禁用', $context);
                    return $this->response('999', config('error.999'));
                }
            }
            return $this->response('1007', config('error.1007'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            return $this->response('1007', config('error.1007'));
        }
    }
    /**
     * 校验验证码
     *
     * @SWG\Post(path="/api/v1/auth/code",
     *   tags={"api-auth"},
     *   summary="校验验证码-可测试-zj",
     *   description="校验验证码",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="formData",
     *     name="mobile",
     *     type="string",
     *     description="手机号",
     *     required=true,
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="type",
     *     type="string",
     *     description="请求短信的类型 forget 忘记密码",
     *     required=true,
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="code",
     *     type="string",
     *     description="验证码",
     *     required=true,
     *   ),
     * @SWG\Response(response="default",   description="操作成功")
     * )
     */
    public function verifyCode(Request $request)
    {
        $type   = $request->get('type');
        $code   = $request->get('code');
        $mobile = $request->get('mobile');
        $context = [
            'method' => __METHOD__,
            'mobile' => $mobile,
            'code'   => $code,
            'type'   => $type,
        ];
        try {
            Log::info('校验验证码', $context);
            $flag = app('sms')->checkRedisVal($type, $code, $mobile);
            if (!$flag) {
                Log::error(config('error.1011'), $context);
                return $this->response(1011, config('error.1011'));
            }
            return $this->response(0, config('error.0'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            return $this->response(1011, config('error.1011'));
        }
    }
    /**
     * 发送短息
     *
     * @SWG\Post(path="/api/v1/auth/sms",
     *   tags={"api-auth"},
     *   summary="发送短息-可测试-zj",
     *   description="发送短息",
     *   operationId="register",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="formData",
     *     name="mobile",
     *     type="string",
     *     description="手机号",
     *     required=true,
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="type",
     *     type="string",
     *     description="请求短信的类型 forget 忘记密码,repwd 修改密码 ",
     *     default="forget",
     *     required=true,
     *   ),
     * @SWG\Response(response="default",  description="操作成功")
     * )
     */
    public function sms(Request $request, UserRepository $userRepository)
    {
        $mobile  = $request->get('mobile');
        $type    = $request->get('type');
        $context = [
                'mobile' => $mobile,
                'type'   => $type,
                'method' => __METHOD__,
            ];
        try {
            $user = $this->getUser();
            $userMobile = $user->mobile;
        } catch (\Exception $ex) {
            $userMobile = '';
        }
        try {
            if (!isMobile($mobile)) {
                Log::error(config('error.1002'), $context);
                return $this->response(1002, config('error.1002'));
            }
            if (!in_array($type, $this->_sms_type)) {
                Log::error(config('error.1001'), $context);
                return $this->response(1001, config('error.1001'));
            }
            $flag = $this->checkMobileExists($mobile, $userRepository);
            if (in_array($type, ['repwd', 'forget'])) {
                if (!$flag) {
                    Log::error(config('error.1003'), $context);
                    return $this->response(1003, config('error.1003'));
                }
            }
            if ($userMobile && $userMobile != $mobile) {
                return $this->response(1005, config('error.1005'));
            }
            $result = app('sms')->send_code($mobile, $type);
            if ($result['error'] == true) {
                return $this->response($result['error'], $result['msg']);
            }
            Log::info(config('error.0'), $context);
            return $this->response(0, config('error.0'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            return $this->ex_response($ex, 1001, config('error.1001'));
        }
    }

    /**
     * 忘记密码
     *
     * @SWG\Post(path="/api/v1/auth/repwd",
     *   tags={"api-auth"},
     *   summary="忘记密码-可测试-zj",
     *   description="忘记密码",
     *   operationId="repwd",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="formData",
     *     name="mobile",
     *     type="string",
     *     description="手机号",
     *     required=true,
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="password",
     *     type="string",
     *     description="密码",
     *     required=true,
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="code",
     *     type="string",
     *     description="短信验证码",
     *     required=true,
     *   ),
     * @SWG\Response(response="default",    description="操作成功")
     * )
     */
    public function repwd(Request $request, UserRepository $userRepository)
    {
        $mobile   = $request->get('mobile');
        $password = $request->get('password');
        $code     = $request->get('code');

        $context = [
                'mobile' => $mobile,
                'method' => __METHOD__,
            ];
        Log::info(config('error.0'), $context);
        try {
            if (!isMobile($mobile)) {
                Log::error(config('error.1014'), $context);
                return $this->response(1014, config('error.1014'));
            }
            $flag = $this->checkMobileExists($mobile, $userRepository);
            if (!$flag) {
                Log::error(config('error.1010'), $context);
                return $this->response(1010, config('error.1010'));
            }

            $flag = app('sms')->checkRedisVal('forget', $code, $mobile);
            if (!$flag) {
                Log::error(config('error.1011'), $context);
                return $this->response(1011, config('error.1011'));
            }
            app('sms')->removeRedisVal('forget', $mobile);

            $where = ['mobile' => $mobile];
            $user  = $userRepository->findWhere($where, ['id', 'password'])->first();

            $data = [
                'password' => password_hash($password, PASSWORD_BCRYPT)
            ];
            $flag = $userRepository->update($data, $user->id);
            if ($flag) {
                Log::info(config('error.0'), $context);
                return $this->response(0, config('error.0'));
            }
            Log::error(config('error.1013'), $context);
            return $this->response(1013, config('error.1013'));
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            return $this->ex_response($ex, 1013, config('error.1013'));
        }
    }

    /**
     * 登录
     *
     * @SWG\Post(path="/api/v1/auth/login",
     *   tags={"api-auth"},
     *   summary="登录-可测试-zj",
     *   description="登录",
     *   operationId="login",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="formData",
     *     name="mobile",
     *     type="string",
     *     description="手机号",
     *     required=true,
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="password",
     *     type="string",
     *     description="密码",
     *     required=true,
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="device_id",
     *     type="string",
     *     description="设备标识",
     *     required=false,
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="device_os",
     *     type="string",
     *     description="设备类型 android | os",
     *     required=false,
     *   ),
     * @SWG\Response(
     *     response=200,
     *     description="Token created",
     * @SWG\Schema(
     *       type="object",
     * @SWG\Property(
     *         property="data",
     *         type="string",
     *         description="jwt的token值",
     *         default="",
     *         example=" "
     *       ),
     * @SWG\Property(
     *         property="error",
     *         type="string",
     *         description="是否有错误信息",
     *         default="",
     *         example="0"
     *       ),
     * @SWG\Property(
     *         property="msg",
     *         type="string",
     *         description="",
     *         default="",
     *         example="登录成功"
     *       )
     *     )
     *   )
     * )
     */
    public function login(Request $request, UserRepository $userRepository, UserAppRepository $userAppRepository, CompanyRepository $companyRepository, CompanySiteRepository $companySiteRepository)
    {
        $mobile = $request->get('mobile');
        $where  = ['mobile' => $mobile];
        $context = [
                'mobile' => $mobile,
                'method' => __METHOD__,
            ];
        Log::info(config('error.0'), $context);

        try {
            if (!$mobile) {
                Log::error(config('error.1019'), $context);
                return $this->response(1019, config('error.1019'));
            }
            if (!isMobile($mobile)) {
                Log::error(config('error.1005'), $context);
                return $this->response(1005, config('error.1005'));
            }

            $flag = $this->checkMobileExists($mobile, $userRepository);
            if (!$flag) {
                Log::error(config('error.1016'), $context);
                return $this->response(1016, config('error.1016'));
            }

            $columns = [
                'id',
                'name',
                'state',
                'mobile',
                'company_id',
                'site_id',
                'billable_id',
                'billable_type',
            ];

            $userGlobal    = $userRepository->findWhere($where, $columns)->first();
            if($userGlobal->state == 0) {
                Log::error('账户已经被禁用', $context);
                return $this->response('999', config('error.999'));
            }
            $user  = $userGlobal->billable()->first();
            $token =  $this->loginByPwd($request);
            $this->registerAppInfo($userGlobal, $request, $userAppRepository);
            $info   = $this->getUserInfo($userGlobal, $companyRepository, $companySiteRepository);
            $info['token'] = $token;

            return $this->response(0, config('error.0'), $info);
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            return $this->ex_response($ex, 1019, config('error.1019'));
        }
    }

    public function registerAppInfo($user, $request, $userAppRepository)
    {
        $deviceId = $request->get('device_id', '');
        $context = [
                'worker_id' => $user->id,
                'device_id' => $deviceId,
                'device_os'     => strtolower($request->get('device_os')),
                'method'    => __METHOD__,
                'msg'       => '注册app',
            ];
        Log::info('', $context);
        if (!$deviceId) {
            return false;
        }
        $this->sentLogoutMsg($user, $deviceId);

        $data = [
            'user_id' => $user->id,
            'device_id' => $deviceId,
            'device_os' => strtolower($request->get('device_os')),
            'is_login'  => 1,
        ];
        $userAppRepository->insertUserApp($data);
    }
    /**
     * 密码登录
     *
     * @author zhangjun@xiaobaiyoupin.com
     *
     * @param mixed Request $request
     *
     * @return mixed
     */
    public function loginByPwd($request)
    {
        $credentials = $request->only('mobile', 'password');
        if (!$token  = JWTAuth::attempt($credentials)) {
            throw new \Exception(config('error.1017'), 1017);
        }
        return $token;
    }

    /**
     * 测试推送
     *
     * @SWG\Post(path="/api/v1/auth/push",
     *   tags={"api-auth"},
     *   summary="测试推送 - zj",
     *   description="测试推送",
     *   operationId="login",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="formData",
     *     name="dev_id",
     *     type="string",
     *     description="个推推送标识",
     *     required=true,
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="title",
     *     type="string",
     *     description="标题",
     *     default="少爷测试",
     *     required=false,
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="content",
     *     type="string",
     *     description="内容",
     *     default="少爷测试",
     *     required=false,
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="url",
     *     type="string",
     *     description="连接",
     *     default="http://baidu.com",
     *     required=false,
     *   ),
     * @SWG\Parameter(
     *     in="formData",
     *     name="type",
     *     type="string",
     *     default="1001",
     *     description="推送类型 '1000' => '退出', '1001' => '首页通知', '2' => 'text',",
     *     required=false,
     *   ),
     * @SWG\Response(response="default",   description="操作成功")
     * )
     */
    public function testpush(Request $request)
    {
        $deviceId = $request->get('dev_id');
        $title = $request->get('title');
        $content  = [
                    'title'     => $request->get('title'),
                    'content'   => $request->get('content'),
                    'type'      => $request->get('type'),
                    'image_url' => $request->get('url'),
                ];
        Log::info('c=auth f=push data='.json_encode($content));
        $driver      = config('push.push_driver');
        $name = 'push.'.config('push.driver').'.'.config('push.push_driver');
        $pushConfig = config($name);
        // var_export($pushService);
        // $config      = config('push.'.$driver.'.'.$pushService);
        $this->instance = (new PushManager())->driver($driver, $pushService);

        $transContent = json_encode($content);
        $result = $this->instance->pushMessageToList([$deviceId], $content, $title, $transContent, false);
        Log::info('push', $result);
        return $result;
    }
    /**
     * h5地址
     *
     * @SWG\Get(path="/api/v1/auth/descurl",
     *   tags={"api-auth"},
     *   summary="h5地址 - zj ",
     *   description="h5地址",
     *   operationId="restate",
     *   produces={"application/json"},
     * @SWG\Response(response="default",     description="操作成功")
     * )
     */
    public function getH5Url()
    {
        $data = $this->h5Url();
        return $this->response(0, config('error.0'), $data);
    }
    /**
     * app升级信息
     *
     * @SWG\get(path="/api/v1/auth/upgrade",
     *   tags={"api-auth"},
     *   summary="app升级信息-zj",
     *   description="app升级信息",
     *   operationId="auth",
     *   produces={"application/json"},
     * @SWG\Parameter(
     *     in="query",
     *     name="device_os",
     *     type="string",
     *     description="设备系统 ios | android",
     *     required=false,
     *   ),
     * @SWG\Response(response="default",     description="操作成功")
     * )
     */
    public function upgrade(Request $request, AppUpgradeRepository $appUpgradeRepository)
    {
        $context = [
            'data'   => $request->all(),
            'method' => __METHOD__,
        ];
        Log::info('升级信息', $context);
        try {
            $deviceOs = $request->get('device_os') ? strtolower($request->get('device_os')) : '';
            if (!$deviceOs) {
                return $this->response(0, config('error.0'));
            }
            $where = [
                'device_os' => $deviceOs,
                'type'      => 'worker'
            ];
            // $upgrade = $appUpgradeRepository->getLatestByWhere($where);
            $upgrade = [];
            return $this->response(0, config('error.0'), $upgrade);
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            return $this->response(0, config('error.0'));
        }
    }

    /**
     * 获取信息常用
     *
     * @SWG\get(path="/api/v1/auth/sysinfo",
     *   tags={"api-auth"},
     *   summary="获取信息常用 -zj",
     *   description="获取信息常用",
     *   operationId="auth",
     *   produces={"application/json"},
     * @SWG\Response(response="default",     description="操作成功")
     * )
     */
    public function sysinfo(Request $request)
    {
        $context = [
            'data'   => $request->all(),
            'msg'    => '获取信息常用',
            'method' => __METHOD__,
        ];
        Log::info('获取信息常用', $context);
        try {
            $output['list']['dispatch'] = config('saas.dispatch');
            $output['technicals_mobile'] = config('saas.technicals_mobile');
            return $this->response(0, config('error.0'), $output);
        } catch (\Exception $ex) {
            Log::error($ex, $context);
            return $this->response(0, config('error.0'));
        }
    }
}
