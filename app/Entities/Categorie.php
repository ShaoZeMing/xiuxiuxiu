<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\Categorie
 *
 * @property int $id
 * @property string $name
 * @property string $desc
 * @property int $parent_id
 * @property int $level
 * @property int $sort
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Brand[] $brands
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $cat_name
 * @property string $cat_desc
 * @property int $cat_parent_id
 * @property int $cat_level
 * @property int $cat_state
 * @property int $cat_sort
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Categorie[] $children
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\File[] $files
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Malfunction[] $malfunctions
 * @property-read \App\Entities\Categorie $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Product[] $products
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatState($value)
 */
class Categorie extends BaseModel
{
    protected $guarded = [];



    public function brands(){
        return $this->belongsToMany(Brand::class,'brand_categories','cat_id','brand_id');
    }

    /**
     * 获取分类下所有的产品
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(){
        return $this->hasMany(Product::class,'cat_id');
    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function malfunctions(){
        return $this->hasMany(Malfunction::class,'cat_id');
    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(){
        return $this->belongsTo(Categorie::class,'cat_parent_id');
    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(){
        return $this->hasMany(Categorie::class,'cat_parent_id');
    }




    public function  transform(){

    }

}
