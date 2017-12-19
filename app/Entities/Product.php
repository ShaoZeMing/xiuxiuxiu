<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\Product
 *
 * @property int $id
 * @property string $name
 * @property string $desc
 * @property int $cat_id
 * @property int $parent_id
 * @property int $level
 * @property int $sort
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model implements Transformable
{
    use TransformableTrait,SequenceTrait;

    protected $fillable = [];




    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(){
        return $this->belongsTo(Brand::class,'parent_id');

    }

    /**
 * @author ShaoZeMing
 * @email szm19920426@gmail.com
 * @return \Illuminate\Database\Eloquent\Relations\HasMany
 */
    public function children(){
        return $this->hasMany(Brand::class,'parent_id');
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cat(){
        return $this->belongsTo(Categorie::class);
    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }


}
