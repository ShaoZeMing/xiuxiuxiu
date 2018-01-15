<?php

namespace App\Entities;

use ShaoZeMing\Merchant\Traits\AdminBuilder;
use ShaoZeMing\Merchant\Traits\ModelTree;


/**
 * App\Entities\Brand
 *
 * @property string $id
 * @property string $brand_name
 * @property string $brand_desc
 * @property int $brand_parent_id
 * @property int $brand_level
 * @property int $brand_state
 * @property int $brand_sort
 * @property string $brand_logo
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Categorie[] $cats
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Brand[] $children
 * @property-read \App\Entities\Brand $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Product[] $products
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereBrandState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Brand whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BrandM extends Brand
{

    use ModelTree, AdminBuilder;

    public $table='brands';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setParentColumn('brand_parent_id');
        $this->setOrderColumn('brand_sort');
        $this->setTitleColumn('brand_name');
    }


    /**
     * Get options for Select field in form.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function selectOptions($prefix='')
    {
        $cats = self::where('brand_state',1)->orWhere('created_id',getMerchantId())->get()->toArray();
        $options = (new static())->buildSelectOptions($cats,0,$prefix);
        return collect($options)->prepend('无', 0)->all();
    }
    /**
     * Get options for Select field in form.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function selectMerchantOptions($prefix='')
    {
        $cats = getMerchantInfo()->brands()->get()->toArray();
        $options = (new static())->buildSelectOptions($cats,0,$prefix);
        return collect($options)->prepend('无', 0)->all();
    }

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


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $value
     * @return string
     */
    public function getBrandLogoAttribute($value)
    {
        if ($value) {
            $img = parse_url($value)['path'];
            $host = rtrim(config('filesystems.disks.admin.url'), '/').'/';
            return $host . $img;
        }
        return $value;
    }


}
