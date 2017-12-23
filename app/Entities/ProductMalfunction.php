<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\ProductMalfunction
 *
 * @property int $product_id
 * @property int $malfunction_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ProductMalfunction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ProductMalfunction whereMalfunctionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ProductMalfunction whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ProductMalfunction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductMalfunction extends Model implements Transformable
{
    use TransformableTrait;
    protected $guarded = [];

}
