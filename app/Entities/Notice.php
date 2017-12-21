<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\Notice
 *
 * @property int $id
 * @property int $state
 * @property int $type
 * @property int $order_id
 * @property string $title
 * @property mixed $content
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Notice whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Notice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Notice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Notice whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Notice whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Notice whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Notice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Notice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Notice extends BaseModel
{

    protected $fillable = [];


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(){
        return $this->belongsTo(Order::class);
    }
}
