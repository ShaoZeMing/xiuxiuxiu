<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;


/**
 * App\Entities\MerchantBill
 *
 * @property string $id
 * @property int $merchant_id
 * @property int $balance
 * @property int $freeze
 * @property int $available
 * @property int $coupon
 * @property int $paid
 * @property int $income
 * @property int $amount
 * @property int $billable_id
 * @property string $billable_type
 * @property int $biz_type
 * @property string $biz_comment
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereBillableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereBillableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereBizComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereBizType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereFreeze($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBill whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantBill extends BaseModel
{
    protected $guarded = [];
    public $incrementing = false;

}
