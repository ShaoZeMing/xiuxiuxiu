<?php

namespace App\Http\Controllers\WX;

use App\Http\Controllers\Controller;
use App\Repositories\WxUserRepositoryEloquent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class AuthController
 * @package App\Http\Controllers\WX
 */
class AuthController extends Controller
{

    protected $app;

    public function __construct()
    {
        $this->app = app('wechat');
    }



    /**
     * 微信事件自动触发接口
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @return string
     */
    public function event(Request $request)
    {
        Log::info('获取请求数据', [$request, __METHOD__]);
        try {
            $server = $this->app->server;
            $msgArr = $server->getMessage();
            Log::info('请求message', [$msgArr]);
            $server->setMessageHandler(function ($message) {
                switch ($message->MsgType) {
                    case 'event':
                        if ($message->Event = 'click') {
                            switch ($message->EventKey) {
                                case 'my_orders':
                                    break;
                                case 'now_activity':
                                    return '新活动正在筹划中，敬请期待！';
                                    break;
                            };
                        }
                        Log::info('收到xiu事件消息', [__METHOD__]);
                        return '收到xiu事件消息';
                        break;
                    case 'text':
                        Log::info('收到xiu文字消息', [__METHOD__]);
                        return '收到xiu文字消息';
                        break;
                    case 'image':
                        Log::info('收到xiu图片消息', [__METHOD__]);
                        return '收到xiu图片消息';
                        break;
                    case 'voice':
                        Log::info('收到xiu语音消息', [__METHOD__]);
                        return '收到xiu语音消息';
                        break;
                    case 'video':
                        Log::info('收到xiu视频消息', [__METHOD__]);
                        return '收到xiu视频消息';
                        break;
                    case 'location':
                        Log::info('收到xiu坐标消息', [__METHOD__]);
                        return '收到xiu坐标消息';
                        break;
                    case 'link':
                        Log::info('收到xiu链接消息', [__METHOD__]);
                        return '收到xiu链接消息';
                        break;
                    default:
                        Log::info('收到xiu其它消息', [__METHOD__]);
                        return '收到xiu其它消息';
                        break;
                }
            });
            return $server->serve();
        } catch (\Exception $e) {
            Log::error($e, [__METHOD__]);
            return 'success';
        }
    }


    /**
     * 网页授权方法
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @param WxUserRepositoryEloquent $wxUserRepository
     * @param $type
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function auth(Request $request, WxUserRepositoryEloquent $wxUserRepository, $type)
    {
        $context = [
            '$request' => $request,
            'method' => __METHOD__,
            'type' => $type,
        ];
        Log::info('获取请求数据',$context);
        try {
            // 获取 OAuth 授权结果用户信息
            $user = $this->app->oauth->user()->toArray();
            Log::info('微信用户授权成功后用户数据', [$user, __METHOD__]);
            $data['openid'] = $user['id'];
            $data['refresh_token'] = $user['original']['refresh_token'];
            //创建用户
            $wxUser = $wxUserRepository->firstOrCreate(['openid' => $user['id']]);
            session(['user' => $user]);
            Log::info('$wxUser用户', [$wxUser]);
            return redirect('api/wx/user/order/index');
        } catch (\Exception $e) {
            Log::error($e, [__METHOD__]);
            return $this->response('1009', '微信登陆失败');
        }


    }

    /**
     * 创建微信菜单
     * @return string
     */
    public function createMenu()
    {
        try {
            Log::info('微信创建菜单start',[__METHOD__]);
            $buttons = [
                [
                    "type" => "view",
                    "name" => "我是用户",
                    "url" => "http://xiu.4d4k.com/wx/user/order/index"
                ],
                [
                    "type" => "click",
                    "name" => "我是商家",
                    "key" => "now_activity"

                ],
                [
                    "type" => "click",
                    "name" => "我是师傅",
                    "key" => "my_orders"
                ],
            ];

            $res = $this->app->menu->add($buttons);
            Log::info('微信创建菜单', [$res, __METHOD__]);
            return 'success';
        } catch (\Exception $e) {
            Log::error($e, ['微信创建菜单失败！', __METHOD__]);
            return $e->getMessage();
        }

    }
}
