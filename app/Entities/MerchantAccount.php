<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;


/**
 * App\Entities\MerchantAccount
 *
 * @property string $id
 * @property int $balance
 * @property int $freeze
 * @property int $available
 * @property int $coupon
 * @property int $paid
 * @property int $income
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\Merchant $merchant
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereFreeze($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantAccount extends BaseModel
{

    protected $guarded = [];
    public $incrementing = false;

    public function merchant(){
      return $this->belongsTo(Merchant::class,'id','id');
    }

}
