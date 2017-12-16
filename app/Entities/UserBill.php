<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\UserBill
 *
 * @property int $id
 * @property int $user_id
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereBillableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereBillableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereBizComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereBizType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereFreeze($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserBill whereUserId($value)
 * @mixin \Eloquent
 */
class UserBill extends Model implements Transformable
{
    use TransformableTrait,SequenceTrait;

    protected $fillable = [];
    public $incrementing = false;

}
