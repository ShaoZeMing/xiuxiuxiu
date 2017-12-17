<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
/**
 * App\Entities\MerchantBrand
 *
 * @property int $id
 * @property int $merchant_id
 * @property int $brand_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBrand whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBrand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBrand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBrand whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantBrand whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantBrand extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];

}
