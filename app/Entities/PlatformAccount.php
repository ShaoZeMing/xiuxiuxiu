<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\PlatformAccount
 *
 * @property int $id
 * @property int $platform_id
 * @property int $balance
 * @property int $freeze
 * @property int $available
 * @property int $coupon
 * @property int $paid
 * @property int $income
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformAccount whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformAccount whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformAccount whereCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformAccount whereFreeze($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformAccount whereIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformAccount wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformAccount wherePlatformId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\PlatformAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PlatformAccount extends BaseModel
{

    protected $fillable = [];

}
