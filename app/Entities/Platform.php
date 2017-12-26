<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;


/**
 * App\Entities\Platform
 *
 * @property string $id
 * @property string $platform_mobile
 * @property string $platform_name
 * @property string $platform_nickname
 * @property string $platform_face
 * @property string $platform_pwd
 * @property string $platform_pay_pwd
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\PlatformAccount $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\PlatformBill[] $bills
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePlatformFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePlatformMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePlatformName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePlatformNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePlatformPayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePlatformPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Platform extends BaseModel
{
    protected $guarded = [];




    public function account()
    {
        return $this->hasOne(PlatformAccount::class,'id','id');
    }

    public function bills()
    {
        return $this->hasMany(PlatformBill::class);
    }
}
