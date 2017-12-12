<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

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
