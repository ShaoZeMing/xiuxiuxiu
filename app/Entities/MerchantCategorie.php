<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\MerchantCategorie
 *
 * @property int $id
 * @property int $merchant_id
 * @property int $cat_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantCategorie whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantCategorie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantCategorie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantCategorie whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantCategorie whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantCategorie extends Model implements Transformable
{
    use TransformableTrait,SequenceTrait;

    protected $fillable = [];

}
