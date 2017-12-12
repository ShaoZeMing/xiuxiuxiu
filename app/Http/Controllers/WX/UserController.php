<?php

namespace App\Http\Controllers\WX;

use App\Repositories\UserRepositoryEloquent;
use App\Repositories\WxUserRepositoryEloquent;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class UserController extends Controller
{


    /**
     * @param Request $request
     * @param WxUserRepositoryEloquent $wxUserRepository
     * @param UserRepositoryEloquent $userRepository
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function userOrderAuthCallback(Request $request,
                                     WxUserRepositoryEloquent $wxUserRepository,
                                     UserRepositoryEloquent $userRepository)
    {
        Log::info('获取请求数据', [$request, __METHOD__]);
        try {
            // 获取 OAuth 授权结果用户信息
            $app = app('wechat');
            $user = $app->oauth->user()->toArray();
            Log::info('微信用户授权成功后用户数据', [$user, __METHOD__]);
            $data['openid'] = $user['id'];
            $data['refresh_token'] = $user['original']['refresh_token'];
            //创建用户
            $wxUser = $wxUserRepository->firstOrCreate(['openid' => $user['id']]);
            $user = $userRepository->firstOrCreate(['wx_user_id' => $wxUser->id]);
            session(['user' => $user]);
            Log::info('user用户', [$user]);
            return redirect('api/wx/user/order/index');
        } catch (\Exception $e) {
            Log::error($e, [__METHOD__]);
            return $this->response('1009', '微信登陆失败');
        }


    }

}
