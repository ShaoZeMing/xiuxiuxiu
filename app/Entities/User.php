<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
use Shaozeming\LumenPostgis\Eloquent\PostgisTrait;

/**
 * App\Entities\User
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
 * @property int $order_cnt
 * @property int $wx_user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereIsNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereOrderCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User wherePayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User wherePwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereWxUserId($value)
 * @mixin \Eloquent
 */
class User extends Model implements Transformable
{
    use TransformableTrait,PostgisTrait,SequenceTrait;

    protected $fillable = [];
    public $incrementing = false;

}
