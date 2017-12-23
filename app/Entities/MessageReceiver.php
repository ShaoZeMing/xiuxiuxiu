<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\MessageReceiver
 *
 * @mixin \Eloquent
 */
class MessageReceiver extends BaseModel
{

    protected $guarded = [];

}
