<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
use Shaozeming\LumenPostgis\Eloquent\PostgisTrait;

/**
 * App\Entities\Site
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereCancelOrderCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereDoingOrderCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereIsNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site wherePayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site wherePwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSuccessOrderCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereWxUserId($value)
 * @mixin \Eloquent
 */
class Site extends Model implements Transformable
{
    use TransformableTrait,PostgisTrait,SequenceTrait;

    protected $fillable = [];
    public $incrementing = false;
}
