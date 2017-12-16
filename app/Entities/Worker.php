<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
use Shaozeming\LumenPostgis\Eloquent\PostgisTrait;

/**
 * App\Entities\Worker
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
 * @property int $brokerage_percent
 * @property int $wx_user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereBrokeragePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereCancelOrderCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereIsNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker wherePayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker wherePwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereSuccessOrderCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWxUserId($value)
 * @mixin \Eloquent
 */
class Worker extends Model implements Transformable
{
    use TransformableTrait,PostgisTrait,SequenceTrait;

    protected $fillable = [];
    public $incrementing = false;

}
