<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * App\Entities\BrandCategorie
 *
 * @property int $brand_id
 * @property int $cat_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\BrandCategorie whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\BrandCategorie whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\BrandCategorie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\BrandCategorie whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BrandCategorie extends BaseModel
{

    protected $guarded = [];

}
