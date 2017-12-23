<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\Platform
 *
 * @property int $id
 * @property string $mobile
 * @property string $name
 * @property string $nickname
 * @property string $face
 * @property string $pwd
 * @property string $pay_pwd
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $platform_mobile
 * @property string $platform_name
 * @property string $platform_nickname
 * @property string $platform_face
 * @property string $platform_pwd
 * @property string $platform_pay_pwd
 * @property-read \App\Entities\PlatformAccount $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\PlatformBill[] $bills
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePlatformFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePlatformMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePlatformName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePlatformNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePlatformPayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePlatformPwd($value)
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
