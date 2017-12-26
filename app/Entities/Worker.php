<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
use Shaozeming\LumenPostgis\Eloquent\PostgisTrait;

/**
 * App\Entities\Worker
 *
 * @property string $id
 * @property string $worker_mobile
 * @property string $worker_name
 * @property string $worker_nickname
 * @property string $worker_face
 * @property string $worker_pwd
 * @property string $worker_pay_pwd
 * @property string $worker_birthday
 * @property int $worker_sex
 * @property int $worker_state
 * @property int $worker_is_notice
 * @property float $worker_lat
 * @property float $worker_lng
 * @property string $worker_province
 * @property string $worker_city
 * @property string $worker_district
 * @property string $worker_address
 * @property int $worker_province_id
 * @property int $worker_city_id
 * @property int $worker_district_id
 * @property string $worker_full_address
 * @property int $worker_cancel_cnt
 * @property int $worker_success_cnt
 * @property int $worker_brokerage_percent
 * @property int $wx_user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\WorkerAccount $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\WorkerBill[] $bills
 * @property-read \App\Entities\WxUser $wxUser
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerBrokeragePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerCancelCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerIsNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerPayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerSuccessCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWxUserId($value)
 * @mixin \Eloquent
 */
class Worker extends BaseModel
{
    use PostgisTrait;

    protected $guarded = [];
    protected $postgisFields = [
        'worker_geom',
    ];


    public function account()
    {
        return $this->hasOne(WorkerAccount::class,'id','id');
    }

    public function bills()
    {
        return $this->hasMany(WorkerBill::class);
    }

    public function wxUser()
    {
        return $this->belongsTo(WxUser::class);
    }



    public function getWorkerFaceAttribute($value)
    {
        if ($value) {
            $img = parse_url($value)['path'];
            $host = rtrim(config('filesystems.disks.admin.url'), '/').'/';
            return $host . $img;
        }
        return $value;
    }
}
