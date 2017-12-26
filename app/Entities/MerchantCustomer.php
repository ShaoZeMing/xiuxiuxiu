<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * App\Entities\MerchantCustomer
 *
 * @property int $merchant_id
 * @property int $customer_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantCustomer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantCustomer whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantCustomer whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantCustomer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantCustomer extends BaseModel
{
    protected $guarded = [];

}
