<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
use ShaoZeMing\Merchant\Traits\AdminBuilder;
use ShaoZeMing\Merchant\Traits\ModelTree;


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
class CategorieM  extends Categorie
{
    use ModelTree, AdminBuilder;

    public $table='categories';

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
    public static function selectOptions($prefix='')
    {
        $cats = self::where('cat_state',1)->orWhere('created_id',getMerchantId())->get()->toArray();
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
        $cats = getMerchantInfo()->cats()->get()->toArray();
        $options = (new static())->buildSelectOptions($cats,0,$prefix);
        return collect($options)->prepend('无', 0)->all();
    }


    public function  transform(){

    }

}
