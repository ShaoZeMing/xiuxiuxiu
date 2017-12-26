<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\WorkerCategorie
 *
 * @property int $worker_id
 * @property int $cat_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerCategorie whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerCategorie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerCategorie whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WorkerCategorie whereWorkerId($value)
 * @mixin \Eloquent
 */
class WorkerCategorie extends Model implements Transformable
{
    use TransformableTrait;
    protected $guarded = [];
    public $incrementing = false;

}
