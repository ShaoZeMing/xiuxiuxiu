<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\Malfunction
 *
 * @property int $id
 * @property string $name
 * @property string $desc
 * @property int $product_id
 * @property int $parent_id
 * @property int $level
 * @property int $sort
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Malfunction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Malfunction extends BaseModel
{

    protected $fillable = [];


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


    public function resolvents()
    {
        return $this->hasMany(Resolvent::class);

    }

}
