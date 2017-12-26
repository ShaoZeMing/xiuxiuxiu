<?php

namespace App\Entities;

use Shaozeming\LumenPostgis\Eloquent\PostgisTrait;

/**
 * App\Entities\Customer
 *
 * @property string $id
 * @property string $customer_mobile
 * @property string $customer_name
 * @property float $customer_lat
 * @property float $customer_lng
 * @property string $customer_province
 * @property string $customer_city
 * @property string $customer_district
 * @property int $customer_province_id
 * @property int $customer_city_id
 * @property int $customer_district_id
 * @property string $customer_address
 * @property string $customer_full_address
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Merchant[] $merchants
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCustomerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCustomerCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCustomerCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCustomerDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCustomerDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCustomerFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCustomerLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCustomerLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCustomerMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCustomerProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereCustomerProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Customer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Customer extends BaseModel
{
    use PostgisTrait;

    protected $guarded = [];
    protected $postgisFields = [
        'customer_geom',
    ];
    protected $postgisTypes = [
        'geom' => [
            'geomtype' => 'geometry',
            'srid' => 4326
        ],
    ];


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * 客户所属企业
     */
    public function merchants(){
        return $this->belongsToMany(Merchant::class);
    }

}
