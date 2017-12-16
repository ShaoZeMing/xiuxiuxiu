<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\MerchantAccount
 *
 * @property int $id
 * @property int $merchant_id
 * @property int $balance
 * @property int $freeze
 * @property int $available
 * @property int $coupon
 * @property int $paid
 * @property int $income
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereFreeze($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantAccount extends Model implements Transformable
{
    use TransformableTrait,SequenceTrait;

    protected $fillable = [];
    public $incrementing = false;

}
