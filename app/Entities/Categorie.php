<?php

namespace App\Entities;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;


/**
 * App\Entities\Categorie
 *
 * @property string $id
 * @property string $cat_name
 * @property string $cat_desc
 * @property int $cat_parent_id
 * @property int $cat_level
 * @property int $cat_state
 * @property int $cat_sort
 * @property string $cat_logo
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Brand[] $brands
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Categorie[] $children
 * @property-read mixed $categorie_logo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Malfunction[] $malfunctions
 * @property-read \App\Entities\Categorie $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Product[] $products
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCatState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Categorie whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Categorie extends BaseModel
{
    use ModelTree, AdminBuilder;
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setParentColumn('cat_parent_id');
        $this->setOrderColumn('cat_sort');
        $this->setTitleColumn('cat_name');
    }


    /**
     * Get options for Select field in form.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function selectOptions()
    {
        $options = (new static())->buildSelectOptions();

        return collect($options)->prepend('无', 0)->all();
    }

//    /**
//     * @return array
//     */
//    public function allNodes() : array
//    {
//        $orderColumn = DB::getQueryGrammar()->wrap($this->orderColumn);
//        $byOrder = $orderColumn.' = 0,'.$orderColumn;
//
//        return static::with('brands')->orderByRaw($byOrder)->get()->toArray();
//    }

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




    public function getCatLogoAttribute($value)
    {
        if ($value) {
            $img = parse_url($value)['path'];
            $host = rtrim(config('filesystems.disks.admin.url'), '/').'/';
            return $host . $img;
        }
        return $value;
    }



    public function  transform(){

    }

}
