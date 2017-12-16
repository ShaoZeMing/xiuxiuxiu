<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * App\Entities\Area
 *
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Area whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Area whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Area whereParentId($value)
 * @mixin \Eloquent
 */
class Area extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];
    public $incrementing = false;
    public $timestamps= false;


    public function parent()
    {
        return $this->belongsTo(Area::class, 'parent_id');
    }

    public function sub()
    {
        return $this->hasMany(Area::class, 'parent_id');
    }

}
