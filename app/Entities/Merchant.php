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
 * @property string $merchant_mobile
 * @property string $merchant_name
 * @property string $merchant_nickname
 * @property string $merchant_face
 * @property string $merchant_pwd
 * @property string $merchant_pay_pwd
 * @property string $merchant_birthday
 * @property int $merchant_sex
 * @property int $merchant_state
 * @property int $merchant_is_notice
 * @property float $merchant_lat
 * @property float $merchant_lng
 * @property string $merchant_province
 * @property string $merchant_city
 * @property string $merchant_district
 * @property string $merchant_address
 * @property string $merchant_full_address
 * @property int $merchant_cancel_cnt
 * @property int $merchant_success_cnt
 * @property int $merchant_doing_cnt
 * @property int $merchant_brokerage_percent
 * @property-read \App\Entities\MerchantAccount $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\MerchantBill[] $bills
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Brand[] $brands
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Categorie[] $cats
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Product[] $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Site[] $sites
 * @property-read \App\Entities\WxUser $wxUser
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantBrokeragePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantCancelCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantDoingCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantIsNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantPayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantSuccessCnt($value)
 * @property int $merchant_province_id
 * @property int $merchant_city_id
 * @property int $merchant_district_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Customer[] $customers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Order[] $orders
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Merchant whereMerchantProvinceId($value)
 */
class Merchant extends BaseModel
{
    use PostgisTrait;

    protected $guarded = [];
    protected $postgisFields = [
        'merchant_geom',
    ];


    public function getMerchantFaceAttribute($value)
    {
        if ($value) {
            $img = parse_url($value)['path'];
            $host = rtrim(config('filesystems.disks.admin.url'), '/').'/';
            return $host . $img;
        }
        return $value;
    }


    public function cats()
    {
        return $this->belongsToMany(Categorie::class, 'merchant_categories','merchant_id','cat_id');

    }

    public function products()
    {
        return $this->belongsToMany(Product::class,'merchant_products');

    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class,'merchant_brands');

    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * 企业关联故障
     */
    public function malfunctions()
    {
        return $this->belongsToMany(Malfunction::class,'merchant_malfunctions');

    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * 企业关联服务类型
     */
    public function serviceTypes()
    {
            return $this->belongsToMany(ServiceType::class,'merchant_service_types');

    }

    public function sites()
    {
        return $this->belongsToMany(Site::class);
    }


    public function account()
    {
        return $this->hasOne(MerchantAccount::class,'id','id');
    }

    public function bills()
    {
        return $this->hasMany(MerchantBill::class);
    }

    public function wxUser()
    {
        return $this->belongsTo(WxUser::class);
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * 企业客户
     */
    public function customers(){
        return $this->belongsToMany(Customer::class);
    }



    public function orders(){
        return $this->morphToMany(Order::class,'createdable');
    }

}
