<?php

namespace App\Http\Controllers\WX;

use App\Entities\Customer;
use App\Entities\Order;
use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepositoryEloquent;
use App\Repositories\CustomerRepositoryEloquent;
use App\Repositories\OrderRepositoryEloquent;
use EasyWeChat\OpenPlatform\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{

    protected $app;

    public function __construct()
    {
        $this->app = app('wechat');
    }

    /**
     * 微信公众号授权事件接口
     *
     * @author zhangjun@xiaobaiyoupin.com
     *
     * @param mixed Request $request
     *
     * @return mixed
     */

    public function auth(Request $request)
    {
        $context = [
            '$request' => $request->all(),
            'method' => __METHOD__,
        ];
        try {
            Log::info('微信授权接收接口', $context);
            $this->app->server->setMessageHandler(function ($event) {
                // 事件类型常量定义在 \EasyWeChat\OpenPlatform\Guard 类里
                switch ($event->InfoType) {
                    case Guard::EVENT_AUTHORIZED: // 授权成功
                        $authorizationInfo = $this->app->getAuthorizationInfo($event->AuthorizationCode);
                        // 保存数据库操作等...
                        Log::info('授权成功', [$authorizationInfo]);
                        break;
                    case Guard::EVENT_UPDATE_AUTHORIZED: // 更新授权
                        // 更新数据库操作等...
                        Log::info('授权更新', [__METHOD__]);
                        break;
                    case Guard::EVENT_UNAUTHORIZED: // 授权取消
                        // 更新数据库操作等...
                        Log::info('授权取消', [__METHOD__]);
                        break;
                }
            });
            return $this->app->server->serve();
        } catch (\Exception $e) {
            Log::error($e, $context);
            return 'success';
        }

    }


    //授权成功跳转页面
    public function targetAuth(Request $request, CompanyRepositoryEloquent $companyRepository, $cid)
    {

        $companyId = hashidsDecode($cid);
        $context = [
            '$request' => $request->all(),
            'companyId' => $companyId,
            'method' => __METHOD__,
        ];
        Log::info('授权成功跳转方法', $context);
        $openPlatform = $this->openPlatform;
        $info = $openPlatform->getAuthorizationInfo();
        Log::info('凭证+更新状态:', [$info->toArray()]);
        $date = [
            'app_id' => $info->get('authorization_info.authorizer_appid'),
            'refresh_token' => $info->get('authorization_info.authorizer_refresh_token'),
        ];
        $company = $companyRepository->update($date, $companyId);
        Log::info('凭证+更新状态:', [$company, $date]);
        return redirect('/');
    }


    /**
     * 微信公众号事件触发接口
     *
     * @author shaozeming@xiaobaiyoupin.com
     *
     * @param mixed Request $request
     *
     * @return mixed
     */
    public function callbackEvent(Request $request, $id)
    {
        Log::info('获取请求数据', [$request, 'app_id' => $id, __METHOD__]);
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
     * 创建微信菜单
     * @return string
     */
    public function createMenu()
    {
        try {
            Log::info('微信创建菜单start'[__METHOD__]);
            $buttons = [
                [
                    "type" => "view",
                    "name" => "我是用户",
                    "url" => "http://xiu.4d4k.com/api/wx/order/index"
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
