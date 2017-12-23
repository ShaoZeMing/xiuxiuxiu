<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\ServiceType
 *
 * @property string $id
 * @property string $service_type_name
 * @property string $service_type_desc
 * @property int $service_type_state
 * @property int $service_type_level
 * @property int $service_type_sort
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Malfunction[] $malfunctions
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereServiceTypeDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereServiceTypeLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereServiceTypeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereServiceTypeSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereServiceTypeState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ServiceType extends BaseModel
{

    protected $guarded = [];


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function malfunctions(){
        return $this->hasMany(Malfunction::class);
    }
}
