<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
class MerchantProduct extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];

}
