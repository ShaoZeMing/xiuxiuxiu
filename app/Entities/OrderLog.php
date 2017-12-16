<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\OrderLog
 *
 * @property int $id
 * @property int $type
 * @property int $order_id
 * @property mixed $data
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\OrderLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\OrderLog whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\OrderLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\OrderLog whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\OrderLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\OrderLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderLog extends Model implements Transformable
{
    use TransformableTrait,SequenceTrait;
    protected $fillable = [];

}
