<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
use Shaozeming\LumenPostgis\Eloquent\PostgisTrait;

class Site extends Model implements Transformable
{
    use TransformableTrait,PostgisTrait,SequenceTrait;

    protected $fillable = [];
    public $incrementing = false;
}
