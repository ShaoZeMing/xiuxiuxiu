<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
/**
 * App\Entities\SiteCategorie
 *
 * @property int $id
 * @property int $site_id
 * @property int $cat_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteCategorie whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteCategorie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteCategorie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteCategorie whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteCategorie whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SiteCategorie extends Model implements Transformable
{
    use TransformableTrait,SequenceTrait;

    protected $fillable = [];
    public $incrementing = false;

}
