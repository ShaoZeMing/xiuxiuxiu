<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\Brand
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Brand extends Model implements Transformable
{
    use TransformableTrait,SequenceTrait;

    protected $fillable = [];


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(){
        return $this->belongsTo(Brand::class);

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(){
        return $this->hasMany(Product::class);
    }




    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files()
    {
        return $this->morphMany(File::class, 'uploadable');
    }


}
