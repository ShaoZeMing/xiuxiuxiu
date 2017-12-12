<?php

namespace App\Http\Controllers\WX;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class PaymentController extends Controller
{





    //微信支付成功通知回调
    public function notify(Request $request)
    {

        Log::info('微信支付回调通知', [$request, __METHOD__]);
        $res = app('payment')->wxNotify();
        return $res;
    }

}
