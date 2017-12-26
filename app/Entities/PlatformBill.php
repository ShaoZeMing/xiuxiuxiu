<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;


/**
 * App\Entities\PlatformBill
 *
 * @property string $id
 * @property int $platform_id
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereBillableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereBillableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereBizComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereBizType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereFreeze($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill wherePlatformId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformBill whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PlatformBill extends BaseModel
{

    protected $guarded = [];
    public $incrementing = false;

}
