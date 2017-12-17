<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
use Shaozeming\LumenPostgis\Eloquent\PostgisTrait;

/**
 * App\Entities\Order
 *
 * @property int $id
 * @property int $order_no
 * @property int $state
 * @property int $order_type
 * @property int $biz_type
 * @property int $cat_id
 * @property string $cat
 * @property int $brand_id
 * @property string $brand
 * @property int $product_id
 * @property string $product
 * @property int $malfunction_id
 * @property string $malfunction
 * @property int $price
 * @property int $pay_price
 * @property string $order_desc
 * @property string $verify_code
 * @property int $pay_state
 * @property string $created_name
 * @property string $created_logo
 * @property int $worker_id
 * @property string $worker_name
 * @property string $worker_mobile
 * @property string $worker_logo
 * @property string $connect_mobile
 * @property string $connect_name
 * @property string $province
 * @property string $city
 * @property string $district
 * @property string $address
 * @property string $full_address
 * @property float $order_lat
 * @property float $order_lng
 * @property string $order_source
 * @property string $accepted_at
 * @property string $booked_at
 * @property string $inspected_at
 * @property string $canceled_at
 * @property string $finished_at
 * @property string $confirmed_at
 * @property string $canceled_desc
 * @property string $finished_desc
 * @property string $inspected_desc
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereBizType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereBookedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCanceledDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereConnectMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereConnectName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCreatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCreatedLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCreatedMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCreatedName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCreatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereFinishedDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereInspectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereInspectedDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereMalfunction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereMalfunctionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereOrderDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereOrderLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereOrderLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereOrderNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereOrderSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereOrderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order wherePayPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order wherePayState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereVerifyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereWorkerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereWorkerLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereWorkerMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereWorkerName($value)
 * @mixin \Eloquent
 * @property int $createdable_id
 * @property string $createdable_type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCreatedableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCreatedableType($value)
 */
class Order extends Model implements Transformable
{
    use TransformableTrait,PostgisTrait,SequenceTrait;

    protected $fillable = [];
    public $incrementing = false;


    /**
     * 产品
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(){
        return $this->belongsTo(Product::class);
    }

    /**
     * 分类
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cat(){
        return $this->belongsTo(Categorie::class);
    }

    /**
     * 品牌
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    /**
     * 师傅
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function worker(){
        return $this->belongsTo(Worker::class);
    }

    /**
     * 故障
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function malfunction(){
        return $this->belongsTo(Malfunction::class);
    }

    /**
     * 创建者
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function createdable(){
        return $this->morphTo();
    }
}
