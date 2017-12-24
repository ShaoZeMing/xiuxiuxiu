<?php

namespace App\Services;

use Ixudra\Curl\Facades\Curl;
use Liyu\Signature\Facade\Signature;

/**
 *  OrderBillingService.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: OrderBillingService.php 2017-03-23 下午1:48 $
 */
class ApiService
{
    public static function request($url, $data, $appId, $method = 'get')
    {
        $data['timestamp'] = time();
        $data['app_id'] = $appId;

        $secretKey = config('signature.sign_key.' . $appId);


        $sign = Signature::signer('hmac')
                         ->setAlgo('sha256')
                         ->setKey($secretKey)
                         ->sign($data);
        $data['sign'] = $sign;

        $response = Curl::to($url)->withData($data)->$method();

        return $response;
    }

    public static function verify($sign, $data, $appId)
    {
        if (!$sign) {
            return false;
        }

        $secretKey = config('signature.sign_key.' . $appId);
        if (!$secretKey) {
            return false;
        }
        return Signature::setAlgo('sha256')
                        ->setKey($secretKey)
                        ->verify($sign, $data);
    }
}
