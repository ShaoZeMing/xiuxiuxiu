<?php

namespace App\Entities;

use App\Traits\HashIdsTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\BaseModel
 *
 * @mixin \Eloquent
 */
class BaseModel extends Model implements Transformable
{
    use TransformableTrait, SequenceTrait,HashIdsTrait;

    public $incrementing = false;

    protected $casts = [
        'id' => 'string',
    ];

}
