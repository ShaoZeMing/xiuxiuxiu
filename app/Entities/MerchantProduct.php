<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\MerchantProduct
 *
 * @property int $merchant_id
 * @property int $brand_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantProduct whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantProduct whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantProduct extends Model implements Transformable
{
    use TransformableTrait;

    protected $guarded = [];

}
