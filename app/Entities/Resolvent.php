<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Resolvent extends BaseModel
{

    protected $fillable = [];



    public function malfunction(){

        return $this->belongsTo(Malfunction::class);
    }

}
