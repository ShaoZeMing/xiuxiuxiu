<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\WxUser
 *
 * @property string $id
 * @property string $wx_unionid
 * @property string $wx_openid
 * @property string $wx_nickname
 * @property string $wx_face
 * @property int $wx_sex
 * @property string $wx_province
 * @property string $wx_city
 * @property string $wx_country
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\Merchant $merchant
 * @property-read \App\Entities\Site $site
 * @property-read \App\Entities\User $user
 * @property-read \App\Entities\Worker $worker
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereWxCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereWxCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereWxFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereWxNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereWxOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereWxProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereWxSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\WxUser whereWxUnionid($value)
 * @mixin \Eloquent
 */
class WxUser extends BaseModel
{
    protected $guarded = [];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(){
       return  $this->hasOne(User::class,'wx_user_id','id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function merchant(){
       return  $this->hasOne(Merchant::class,'wx_user_id','id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function site(){
       return  $this->hasOne(Site::class,'wx_user_id','id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function worker(){
       return  $this->hasOne(Worker::class,'wx_user_id','id');
    }
}
