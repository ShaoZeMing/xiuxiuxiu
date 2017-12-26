<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\UserAccount
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserAccount whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserAccount whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserAccount whereCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserAccount whereFreeze($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserAccount whereIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserAccount wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\UserAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserAccount extends BaseModel
{

    protected $guarded = [];

}
