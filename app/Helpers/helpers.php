<?php
/**
 * 元转换分
 *
 * @author gengzhiguo@xiongmaojinfu.com
 *
 * @param $amount
 *
 * @return mixed
 */
function yuanToFen($amount)
{
    return intval(floatval($amount) * 100);
}

/**
 * 分转换元
 *
 * @author gengzhiguo@xiongmaojinfu.com
 *
 * @param $amount
 *
 * @return float
 */
function fenToYuan($amount)
{
    return floatval(intval($amount) / 100);
}

if (!function_exists('isCardID')) {

    /**
     * 验证身份证号
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param $sId
     *
     * @return bool|string
     */
    function isCardID($sId)
    {
        $aCity = array(
            11 => "北京", 12 => "天津", 13 => "河北", 14 => "山西", 15 => "内蒙古", 21 => "辽宁", 22 => "吉林", 23 => "黑龙江", 31 => "上海",
            32 => "江苏", 33 => "浙江", 34 => "安徽", 35 => "福建", 36 => "江西", 37 => "山东", 41 => "河南", 42 => "湖北", 43 => "湖南",
            44 => "广东", 45 => "广西", 46 => "海南", 50 => "重庆", 51 => "四川", 52 => "贵州", 53 => "云南", 54 => "西藏", 61 => "陕西",
            62 => "甘肃", 63 => "青海", 64 => "宁夏", 65 => "新疆", 71 => "台湾", 81 => "香港", 82 => "澳门", 91 => "国外",
        );
        $iSum = 0;
        $info = "";
        //长度验证
        if (!preg_match('/^\d{17}(\d|x)$/i', $sId)) {
            throw new \Exception('请检查身份证号');
        }
        //地区验证
        if (!array_key_exists(intval(substr($sId, 0, 2)), $aCity)) {
            throw new \Exception('请检查身份证号');
        }
        //18位身份证处理
        $sBirthday = substr($sId, 6, 4) . '-' . substr($sId, 10, 2) . '-' . substr($sId, 12, 2);
        try {
            $d = new DateTime($sBirthday);
            $dd = $d->format('Y-m-d');
            if ($sBirthday != $dd) {
                throw new \Exception('请检查身份证号');
            }
        } catch (\Exception $e) {
            throw new \Exception('请检查身份证号');
        }

        // 取出本体码
        $sIdBase = substr($sId, 0, 17);

        // 取出校验码
        $verifyCode = substr($sId, 17, 1);

        // 加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

        // 校验码对应值
        $verifyCodeList = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

        // 根据前17位计算校验码
        $total = 0;
        for ($i = 0; $i < 17; $i++) {
            $total += intval(substr($sIdBase, $i, 1)) * $factor[$i];
        }

        // 取模
        $mod = $total % 11;

        // 比较校验码
        if ($verifyCode != $verifyCodeList[$mod]) {
            return '1906';
            throw new \Exception('请检查身份证号');
        }

        return true; //aCity[parseInt(sId.substr(0,2))]+","+sBirthday+","+(sId.substr(16,1)%2?"男":"女")
    }
}

/**
 *  Common_helper.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: Common_helper.php 2015-09-15 下午7:05 $
 */

if (!function_exists('isMobile')) {
    /**
     * Numeric
     *
     * @param   string $mobile
     *
     * @return  bool
     */
    function isMobile($mobile)
    {
        return (!preg_match("/^(?:13\d|14\d|17\d|15\d|18[0123456789])-?\d{5}(\d{3}|\*{3})$/", $mobile)) ? false : true;
    }
}

if (!function_exists('hashidsDecode')) {
    function hashidsDecode($encodeStr)
    {
        // 字符串的数字也会导致解码
        $encodeStr = is_numeric($encodeStr) ? intval($encodeStr) : $encodeStr;
        $decode = Hashids::decode($encodeStr);
        return isset($decode[0]) ? $decode[0] : $encodeStr;
    }
}

if (!function_exists('hashIdEncode')) {
    function hashIdEncode($id)
    {
        $encodeStr = is_numeric($id) ? intval($id) : $id;
//        dd($encodeStr);
        return Hashids::encode($encodeStr);
    }
}


/*获取后台登陆者ID*/
if (!function_exists('getAdminAuthInfo')) {
    function getAdminAuthInfo()
    {
        $user = auth('admin')->user();
        if ($user) {
            return $user;
        }
        throw new Exception('未登录');
    }
}


/*获取登陆id*/
if (!function_exists('getAdminAuthId')) {
    function getAdminAuthId()
    {
        $user = getAdminAuthInfo();
        if ($user) {
            return $user->id;
        } else {
            return 0;
        }
    }
}
if (!function_exists('getAdminAuthName')) {
    function getAdminAuthName()
    {
        $user = getAdminAuthInfo();
        if ($user) {
            return $user->name;
        } else {
            return 0;
        }
    }
}

/*获取企业登陆信息*/
if (!function_exists('getMerchantAuthInfo')) {
    function getMerchantAuthInfo()
    {
        $user = auth('merchant')->user();
        if ($user) {
            return $user;
        }
        throw new Exception('未登录');
    }
}


/*获取登陆id*/
if (!function_exists('getMerchantAuthId')) {
    function getMerchantAuthId()
    {
        $user = getMerchantAuthInfo();
            return $user->id;

    }
}

if (!function_exists('getMerchantAuthName')) {
    function getMerchantAuthName()
    {
        $user = getMerchantAuthInfo();
        return $user->name;

    }
}


/*获取登陆者商家*/
if (!function_exists('getMerchantInfo')) {
    function getMerchantInfo()
    {
        $user = getMerchantAuthInfo();
        return  $user->merchant;
    }
}

/*获取登陆者商家ID*/
if (!function_exists('getMerchantId')) {
    function getMerchantId()
    {
        $user = getMerchantAuthInfo();
        return $user->merchant_id;
    }
}


/*格式化金额*/
if (!function_exists('formatMoney')) {
    function formatMoney($money, $num = 3, $flag = ',')
    {
        if (strpos($money, '.') === false) {
            $money = intval($money) . '.00';
        } else {
            list($integer, $decimal) = explode('.', $money);
            if (strlen($decimal) < 2) {
                $money = floatval(number_format(floatval($money), 2, '.', '')) . '0';
            } elseif (strlen($decimal) >= 2 && !(int)$decimal == false) {
                $money = floatval(number_format(floatval($money), 2, '.', ''));
            } elseif (strlen($decimal) >= 2 && !(int)$decimal == true) {
                $money = intval($money) . ".00";
            } else {
                $money = intval($money) . '.00';
            }
        }
        // list($yuan, $fen) = explode('.', $money);
        // $revMoney = strrev($yuan);
        // $revMoneyArr = str_split($revMoney, $num);
        // $revMoney = implode($revMoneyArr, ',');
        // $revMoney = strrev($revMoney);
        // $money = $revMoney . '.' . $fen;
        return (string)$money;
    }
}




//截取字符
if (!function_exists('strLimit')) {

    function strLimit($value, $limit = 100, $end = '...')
    {
        $strlen = mb_strlen($value, 'UTF-8');
        $posLimit = abs($limit);

        if ($limit < 0) {
            if ($strlen <= $posLimit) {
                return $end;
            } else {
                $str = rtrim(mb_substr($value, 0, ($strlen + $limit), 'UTF-8')) . $end;
            }
        } else {
            if ($strlen <= $posLimit) {
                return $value;
            } else {
                $str = rtrim(mb_substr($value, 0, $limit, 'UTF-8')) . $end;
            }
        }

        return $str;
    }
}
if (!function_exists('isValidUrl')) {
    function isValidUrl($path)
    {
        if (!preg_match('~^(#|//|https?://|mailto:|tel:)~', $path)) {
            return filter_var($path, FILTER_VALIDATE_URL) !== false;
        }

        return true;
    }
}

if (!function_exists('objectToArray')) {

    function objectToArray($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)objectToArray($v);
            }
        }

        return $obj;
    }
}

if (!function_exists('myAsset')) {
    function myAssetMix($path, $secure = null, $manifestDirectory = '')
    {
        if (env('APP_ENV') === 'production') {
            $path = '/production/' . ltrim($path, '/');

            return mix($path, $manifestDirectory);
        } else {
            $path = '/development/' . ltrim($path, '/');

            return asset($path, $secure);
        }
    }
}

// 生成短网址
if (!function_exists('generateShortUrl')) {

    function generateShortUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://dwz.cn/create.php");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = array('url' => $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $strRes = curl_exec($ch);
        curl_close($ch);
        $arrResponse = json_decode($strRes, true);
        if ($arrResponse['status'] != 0) {
            /**错误处理*/
            //return iconv('UTF-8', 'GBK', $arrResponse['err_msg']);
            return false;
        }
        /** tinyurl */
        return $arrResponse['tinyurl'];
    }

}

if (!function_exists('tinyurl')) {

    function tinyurl($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}


if (!function_exists('formatDate')) {

    //获取凌晨时间戳
    function formatDate($time = 0, $oldDay = 0)
    {
        $time = $time ? $time : time();
        if ($oldDay > 0) {
            if (is_numeric($time)) {
                $time = strtotime(date('Y-m-d'), $time);
                return intval($time - ($oldDay * 24 * 3600));
            } else {
                return intval(strtotime(date('Y-m-d', strtotime($time))) - ($oldDay * 24 * 3600));
            }
        } else {
            if (is_numeric($time)) {
                $time = strtotime(date('Y-m-d'), $time);
                return intval($time);
            } else {
                return intval(strtotime(date('Y-m-d', strtotime($time))));
            }
        }
    }
}

