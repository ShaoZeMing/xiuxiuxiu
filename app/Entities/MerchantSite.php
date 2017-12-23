<?php

namespace App\Entities;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
/**
 * App\Entities\MerchantSite
 *
 * @property int $id
 * @property int $merchant_id
 * @property int $site_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantSite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantSite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantSite whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantSite whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\MerchantSite whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantSite extends Model implements Transformable
{
    use TransformableTrait;

    protected $guarded = [];

}
