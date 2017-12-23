<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\WorkerBill
 *
 * @property int $id
 * @property int $worker_id
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereBillableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereBillableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereBizComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereBizType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereFreeze($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerBill whereWorkerId($value)
 * @mixin \Eloquent
 */
class WorkerBill extends BaseModel
{
    protected $guarded = [];

}
