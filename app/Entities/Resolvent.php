<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;


/**
 * App\Entities\Resolvent
 *
 * @property string $id
 * @property int $malfunction_id
 * @property string $resolvent_name
 * @property string $resolvent_desc
 * @property string $resolvent_url
 * @property int $resolvent_state
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\Malfunction $malfunction
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Resolvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Resolvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Resolvent whereMalfunctionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Resolvent whereResolventDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Resolvent whereResolventName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Resolvent whereResolventState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Resolvent whereResolventUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Resolvent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Resolvent extends BaseModel
{

    protected $guarded = [];



    public function malfunction(){

        return $this->belongsTo(Malfunction::class);
    }

}
