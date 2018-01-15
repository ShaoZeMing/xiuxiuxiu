<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;


/**
 * App\Entities\Malfunction
 *
 * @property string $id
 * @property string $malfunction_name
 * @property string $malfunction_desc
 * @property int $cat_id
 * @property int $service_type_id
 * @property int $malfunction_state
 * @property int $malfunction_level
 * @property int $malfunction_sort
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\Categorie $cat
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Product[] $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Resolvent[] $resolvents
 * @property-read \App\Entities\ServiceType $serviceType
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereMalfunctionDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereMalfunctionLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereMalfunctionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereMalfunctionSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereMalfunctionState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Malfunction extends BaseModel
{

    protected $guarded = [];


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
    public function serviceType(){
        return $this->belongsTo(ServiceType::class,'service_type_id');

    }



    public function products()
    {
        return $this->belongsToMany(Product::class,'product_malfunctions');

    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function merchants(){
        return $this->belongsToMany(Merchant::class,'merchant_malfunctions');
    }


    public function resolvents()
    {
        return $this->hasMany(Resolvent::class);

    }

}
