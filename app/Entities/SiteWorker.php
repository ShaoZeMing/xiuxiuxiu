<?php

namespace App\Entities;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
/**
 * App\Entities\SiteWorker
 *
 * @property int $id
 * @property int $site_id
 * @property int $worker_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteWorker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteWorker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteWorker whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteWorker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\SiteWorker whereWorkerId($value)
 * @mixin \Eloquent
 */
class SiteWorker extends Model implements Transformable
{
    use TransformableTrait;
    protected $guarded = [];

}
