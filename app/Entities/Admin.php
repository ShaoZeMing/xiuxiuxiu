<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\Admin
 *
 * @property int $id
 * @property string $mobile
 * @property string $name
 * @property string $email
 * @property string $face
 * @property string $pwd
 * @property string $pay_pwd
 * @property string $birthday
 * @property int $sex
 * @property int $state
 * @property string $login_ip
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin whereFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin whereLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin wherePayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin wherePwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Admin whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Admin extends Model implements Transformable
{
    use TransformableTrait, SequenceTrait;


    protected $fillable = [];
    public $incrementing = false;

}
