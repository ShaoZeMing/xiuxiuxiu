<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\WxUser
 *
 * @property int $id
 * @property string $unionid
 * @property string $openid
 * @property string $nickname
 * @property string $face
 * @property int $sex
 * @property string $province
 * @property string $city
 * @property string $country
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WxUser extends Model implements Transformable
{
    use TransformableTrait,SequenceTrait;

    protected $fillable = [];
    public $incrementing = false;


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(){
       return  $this->hasOne(User::class,'wx_user_id','id');
    }
}
