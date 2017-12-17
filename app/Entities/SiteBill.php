<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
class SiteBill extends Model implements Transformable
{
    use TransformableTrait;
    use TransformableTrait,SequenceTrait;

    protected $fillable = [];
    public $incrementing = false;

}
