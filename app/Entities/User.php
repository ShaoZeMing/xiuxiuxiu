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
 * @property string $id
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
 * @property int $user_province_id
 * @property string $user_city
 * @property int $user_city_id
 * @property string $user_district
 * @property int $user_district_id
 * @property string $user_address
 * @property string $user_full_address
 * @property int $user_order_cnt
 * @property int $wx_user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\UserAccount $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\UserBill[] $bills
 * @property-read \App\Entities\WxUser $wxUser
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserDistrictId($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUserState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereWxUserId($value)
 * @mixin \Eloquent
 */
class User extends BaseModel
{
    use PostgisTrait;

    protected $guarded = [];
    protected $postgisFields = [
        'user_geom',
    ];


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


    public function getUserFaceAttribute($value)
    {
        if ($value) {
            $img = parse_url($value)['path'];
            $host = rtrim(config('filesystems.disks.admin.url'), '/').'/';
            return $host . $img;
        }
        return $value;
    }
}
