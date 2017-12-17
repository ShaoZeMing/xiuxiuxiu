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
 * @property-read \App\Entities\Area $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Area[] $sub
 */
class Area extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];
    public $incrementing = false;
    public $timestamps= false;


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Area::class, 'parent_id');
    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sub()
    {
        return $this->hasMany(Area::class, 'parent_id');
    }

}
