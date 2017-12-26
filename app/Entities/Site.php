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
 * @property string $id
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
 * @property int $site_province_id
 * @property int $site_city_id
 * @property int $site_district_id
 * @property string $site_address
 * @property string $site_full_address
 * @property int $site_cancel_cnt
 * @property int $site_success_cnt
 * @property int $site_doing_cnt
 * @property int $site_brokerage_percent
 * @property int $wx_user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\SiteAccount $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\SiteBill[] $bills
 * @property-read \App\Entities\WxUser $wxUser
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteBrokeragePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteCancelCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteDistrictId($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSitePwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereSiteSuccessCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Site whereWxUserId($value)
 * @mixin \Eloquent
 */
class Site extends BaseModel
{
    use PostgisTrait;

    protected $guarded = [];
    protected $postgisFields = [
        'site_geom',
    ];


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
