<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\Comment
 *
 * @property int $id
 * @property int $type
 * @property int $stars
 * @property int $worker_id
 * @property int $order_id
 * @property int $commentable_id
 * @property string $commentable_type
 * @property mixed $content
 * @property string $other_content
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Comment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Comment whereOtherContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Comment whereStars($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Comment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Comment whereWorkerId($value)
 * @mixin \Eloquent
 */
class Comment extends Model implements Transformable
{
    use TransformableTrait,SequenceTrait;

    protected $fillable = [];

}
