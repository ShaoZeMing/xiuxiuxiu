<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
use Shaozeming\LumenPostgis\Eloquent\PostgisTrait;

/**
 * App\Entities\Merchant
 *
 * @property int $id
 * @property string $mobile
 * @property string $name
 * @property string $nickname
 * @property string $face
 * @property string $pwd
 * @property string $pay_pwd
 * @property string $birthday
 * @property int $sex
 * @property int $state
 * @property int $is_notice
 * @property float $lat
 * @property float $lng
 * @property string $province
 * @property string $city
 * @property string $district
 * @property string $address
 * @property string $full_address
 * @property int $cancel_order_cnt
 * @property int $success_order_cnt
 * @property int $doing_order_cnt
 * @property int $wx_user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereCancelOrderCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereDoingOrderCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereIsNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant wherePayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant wherePwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereSuccessOrderCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereWxUserId($value)
 * @mixin \Eloquent
 */
class Merchant extends BaseModel
{
    use PostgisTrait;

    protected $fillable = [];
    public $incrementing = false;

}
