<?php

namespace App\Entities;

use Shaozeming\LumenPostgis\Eloquent\PostgisTrait;

class Customer extends BaseModel
{
    use PostgisTrait;

    protected $guarded = [];
    protected $postgisFields = [
        'customer_geom',
    ];
    protected $postgisTypes = [
        'geom' => [
            'geomtype' => 'geometry',
            'srid' => 4326
        ],
    ];


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * 客户所属企业
     */
    public function merchants(){
        return $this->belongsToMany(Merchant::class);
    }

}
