<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\Platform
 *
 * @property int $id
 * @property string $mobile
 * @property string $name
 * @property string $nickname
 * @property string $face
 * @property string $pwd
 * @property string $pay_pwd
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform wherePwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Platform whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Platform extends Model implements Transformable
{
    use TransformableTrait,SequenceTrait;

    protected $fillable = [];
    public $incrementing = false;

}
