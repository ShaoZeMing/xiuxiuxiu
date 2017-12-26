<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;


/**
 * App\Entities\Product
 *
 * @property string $id
 * @property string $product_name
 * @property string $product_desc
 * @property int $cat_id
 * @property int $brand_id
 * @property string $product_version
 * @property string $product_size
 * @property int $product_level
 * @property int $product_state
 * @property int $product_sort
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\Brand $brand
 * @property-read \App\Entities\Categorie $cat
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Brand[] $children
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Malfunction[] $malfunctions
 * @property-read \App\Entities\Brand $parent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereProductDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereProductLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereProductSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereProductSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereProductState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereProductVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends BaseModel
{

    protected $guarded = [];

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(){
        return $this->belongsTo(Brand::class,'parent_parent_id');

    }

    /**
 * @author ShaoZeMing
 * @email szm19920426@gmail.com
 * @return \Illuminate\Database\Eloquent\Relations\HasMany
 */
    public function children(){
        return $this->hasMany(Brand::class,'parent_parent_id');
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
        return $this->belongsTo(Brand::class);
    }




    public function malfunctions()
    {
        return $this->belongsToMany(Malfunction::class,'product_malfunctions');
    }

}
