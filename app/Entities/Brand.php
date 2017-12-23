<?php

namespace App\Entities;

use App\Traits\HashIdsTrait;
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
 * @property string $brand_name
 * @property string $brand_desc
 * @property int $brand_parent_id
 * @property int $brand_level
 * @property int $brand_state
 * @property int $brand_sort
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Brand[] $children
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\File[] $files
 * @property-read \App\Entities\Brand $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Product[] $products
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandState($value)
 */
class Brand extends BaseModel
{

    protected $guarded = [];
    
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
        return $this->hasMany(Brand::class,'brand_parent_id');
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(){
        return $this->hasMany(Product::class,'brand_parent_id');
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cats(){
        return $this->belongsToMany(Categorie::class,'brand_categories','brand_id','cat_id');
    }


}
