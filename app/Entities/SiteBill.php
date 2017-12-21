<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
/**
 * App\Entities\SiteBill
 *
 * @property int $id
 * @property int $site_id
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereBillableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereBillableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereBizComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereBizType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereFreeze($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteBill whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SiteBill extends BaseModel
{
    protected $fillable = [];

}
