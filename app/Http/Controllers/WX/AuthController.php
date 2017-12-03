<?php

namespace App\Http\Controllers\WX;

use App\Http\Controllers\Controller;
use App\Repositories\WxUserRepositoryEloquent;
use EasyWeChat\OpenPlatform\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{


    protected $_sms_type = [
        'forget',
        'repwd'
    ];

    protected $wxUserRepository;
    protected $wx;

    public function __construct(WxUserRepositoryEloquent $wxUserRepository)
    {
        $this->wxUserRepository = $wxUserRepository;
        $this->wx = app('wechat')->server;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * 微信的事件触发接口
     *
     * @return string
     */
    public function event(Request $request)
    {

        Log::info('获取请求数据', [$request, __METHOD__]);
        $app = app('wechat')->server;
        $msgArr = $app->getMessage();
        Log::info('请求message',$msgArr);
        $app->setMessageHandler(function ($message) {
            switch ($message->MsgType) {
                case 'event':
                    return '收到事件消息';
                    break;
                case 'text':
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }
        });
        return $app->serve();
    }


    //创建微信自定义菜单
    public function createMenu(Request $request)
    {

        $context = [
            'request' => $request->all(),
            'method' => __METHOD__,
        ];
        try {
            $jsonmenu = '{
                            "button": [
                                {
                                    "name": "我的博客", 
                                    "sub_button": [
                                        {
                                            "type": "view", 
                                            "name": "微信下单", 
                                            "url": "http://shouhou.yipinxiaobai.com/api/v1/weixin/orders/VKLX2MVeAwez/index"
                                        }, 
                                        {
                                            "type": "view", 
                                            "name": "PHP", 
                                            "url": "http://blog.4d4k.com/category/php/"
                                        }, 
                                        {
                                            "type": "view", 
                                            "name": "SQL", 
                                            "url": "http://blog.4d4k.com/category/sql/"
                                        }
                                    ]
                                }, 
                                {
                                    "name": "扫一扫", 
                                    "sub_button": [
                                        {
                                            "type": "scancode_waitmsg", 
                                            "name": "扫码带提示", 
                                            "key": "sao_ma_ti_shi", 
                                            "sub_button": [ ]
                                        }, 
                                        {
                                            "type": "scancode_push", 
                                            "name": "扫码推事件", 
                                            "key": "sao_ma_tui", 
                                            "sub_button": [ ]
                                        }, 
                                        {
                                            "type": "click", 
                                            "name": "今日歌曲", 
                                            "key": "V1001_TODAY_MUSIC"
                                        }
                                    ]
                                }, 
                                {
                                    "name": "发图", 
                                    "sub_button": [
                                        {
                                            "type": "pic_sysphoto", 
                                            "name": "拍照发图", 
                                            "key": "pai_zhao", 
                                            "sub_button": [ ]
                                        }, 
                                        {
                                            "type": "pic_photo_or_album", 
                                            "name": "拍照or相册", 
                                            "key": "pai_zhao_or_photos", 
                                            "sub_button": [ ]
                                        }, 
                                        {
                                            "type": "pic_weixin", 
                                            "name": "微信相册发图", 
                                            "key": "wixin_photos"
                                        }, 
                                        {
                                            "type": "location_select", 
                                            "name": "发送位置", 
                                            "key": "address"
                                        }
                                    ]
                                }
                            ]
                        }';
        } catch (\Exception $e) {
            Log::info($e, $context);
        }
    }





    public function orderCreate(){

        return view('weixin.order_create');
    }




    public function sendNotice(){
        $app = app('wechat');
        $notice = $app->notice;
//        $templateId = $notice->addTemplate('模板公共ID');
//        Log::info('创建模板ID',[$templateId,__METHOD__]);
        $templateArr = $notice->getPrivateTemplates();
        Log::info('模板列表',[$templateArr,__METHOD__]);
        $messageId = $notice->send([
            'touser' => 'oYzfov2raQuxOG0S_Mv4eoX69Cps',
            'template_id' => 'MhTmjXb8TT9Ec40EpeE3xZcVzE8hHqPEIJZtJOj3ozw',
            'url' => 'http://shouhou.yipinxiaobai.com/api/v1/weixin/orders/536969186711176198/show',
            'data' => [
                "title"    => array("下单成功！", '#555555'),
                "desc" => array("我们会尽快与您取得联系确认上门维修时间，请保持电话畅通。", "#336699"),
                "order_no" => array("171201100201302634", "#FF0000"),
                "service_mode" => array("邮寄", "#888888"),
            ],
        ]);
        Log::info('模板消息ID',[$messageId,__METHOD__]);
        $messageId = $notice->send([
            'touser' => 'oYzfov2raQuxOG0S_Mv4eoX69Cps',
            'template_id' => 'E5FVz2OunMtIp9aEje3bF3n9dpZSX_McBuv2rGVTMbM',
            'url' => 'http://shouhou.yipinxiaobai.com/api/v1/weixin/orders/536880791171367940/show',
            'data' => [
                "title"    => array("下单成功！", '#555555'),
                "desc" => array("已安排工程师上门", "#336699"),
                "order_no" => array("171130103701752935", "#FF0000"),
                "service_mode" => array("上门", "#888888"),
                "worker_name" => array("国强师傅-18513117316", "#888888"),
                "booked_at" => array("2017-11-30 12:00:00", "#888888"),
                "remark" => array("请保持电话畅通，等待上门。", "#888888"),
            ],
        ]);
        Log::info('模板消息ID',[$messageId,__METHOD__]);
        return '模板消息发送成功';
    }

    public function createNotice(){
        $notice = "	{{ title.DATA }}\n {{ desc.DATA }}\n 工单号：{{ order_no.DATA }}\n 服务方式：{{ service_mode.DATA }}";
        $app = app('wechat');
//        $app = new Application([]);
        $notice = $app->notice;
        $templateId = $notice->addTemplate(6);
        $templateArr = $notice->getPrivateTemplates();
    }

}
