<?php

namespace App\Http\Controllers\WX;

use App\Repositories\CategorieRepositoryEloquent;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

/**
 * 用户工单控制器
 * Class UserOrderController
 * @package App\Http\Controllers\WX
 */
class UserOrderController extends Controller
{


    /**
     * @param CategorieRepositoryEloquent $categorieRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(CategorieRepositoryEloquent $categorieRepository)
    {
        $context = [
            'method' => __METHOD__,
        ];
        try {
//            $user = session('user');
            $user = config('wechat.mock_user');
            Log::info('微信用户下单首页', $context);
            if (!$user) {
             $oauth = app('wechat')->oauth;
                $response = $oauth->redirect(url('/wx/auth/user'));
                return $response;
            } else {
                $cats = $categorieRepository->getCats();
                $data['data'] = $cats;
                $data['user'] = $user;
                return view('wx.user.order.index',$data);
            }
        } catch (\Exception $e) {
            Log::error($e, $context);
            return $this->response(2424, '发送错误了');
        }
    }
}
