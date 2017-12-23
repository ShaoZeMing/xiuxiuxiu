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
 * @property string $user_mobile
 * @property string $user_name
 * @property string $user_nickname
 * @property string $user_face
 * @property string $user_pwd
 * @property string $user_pay_pwd
 * @property string $user_birthday
 * @property int $user_sex
 * @property int $user_state
 * @property int $user_is_notice
 * @property float $user_lat
 * @property float $user_lng
 * @property string $user_province
 * @property string $user_city
 * @property string $user_district
 * @property string $user_address
 * @property string $user_full_address
 * @property int $user_order_cnt
 * @property-read \App\Entities\UserAccount $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\UserBill[] $bills
 * @property-read \App\Entities\WxUser $wxUser
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserIsNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserOrderCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserPayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserState($value)
 */
class User extends BaseModel
{
    use PostgisTrait;

    protected $guarded = [];



    public function account()
    {
        return $this->hasOne(UserAccount::class,'id','id');
    }

    public function bills()
    {
        return $this->hasMany(UserBill::class);
    }

    public function wxUser()
    {
        return $this->belongsTo(WxUser::class);
    }
}
