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
 * @property string $site_mobile
 * @property string $site_name
 * @property string $site_nickname
 * @property string $site_face
 * @property string $site_pwd
 * @property string $site_pay_pwd
 * @property string $site_birthday
 * @property int $site_sex
 * @property int $site_state
 * @property int $site_is_notice
 * @property float $site_lat
 * @property float $site_lng
 * @property string $site_province
 * @property string $site_city
 * @property string $site_district
 * @property string $site_address
 * @property string $site_full_address
 * @property int $site_cancel_cnt
 * @property int $site_success_cnt
 * @property int $site_doing_cnt
 * @property int $site_brokerage_percent
 * @property-read \App\Entities\SiteAccount $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\SiteBill[] $bills
 * @property-read \App\Entities\WxUser $wxUser
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteBrokeragePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteCancelCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteDoingCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteIsNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSitePayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSitePwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteSuccessCnt($value)
 */
class Site extends BaseModel
{
    use PostgisTrait;

    protected $guarded = [];



    public function account()
    {
        return $this->hasOne(SiteAccount::class,'id','id');
    }

    public function bills()
    {
        return $this->hasMany(SiteBill::class);
    }

    public function wxUser()
    {
        return $this->belongsTo(WxUser::class);
    }
}
